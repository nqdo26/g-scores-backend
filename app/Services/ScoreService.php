<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\DB;

class ScoreService
{
    /**
     * Valid subjects mapping
     */
    private const SUBJECT_NAMES = [
        'toan' => 'Toán',
        'ngu_van' => 'Ngữ Văn',
        'ngoai_ngu' => 'Ngoại Ngữ',
        'vat_li' => 'Vật Lý',
        'hoa_hoc' => 'Hóa Học',
        'sinh_hoc' => 'Sinh Học',
        'lich_su' => 'Lịch Sử',
        'dia_li' => 'Địa Lý',
        'gdcd' => 'GDCD',
    ];

    /**
     * Check scores by registration number (SBD)
     */
    public function checkScoreByRegistrationNumber(string $sbd): ?array
    {
        // Normalize SBD: pad with zeros to ensure 8 digits
        $sbd = str_pad($sbd, 8, '0', STR_PAD_LEFT);
        
        $student = Student::where('sbd', $sbd)->first();

        if (!$student) {
            return null;
        }

        $result = [
            'sbd' => $student->sbd,
            'scores' => $student->scores,
        ];

        if ($student->hasGroupAScores()) {
            $result['groupA'] = [
                'total' => $student->group_a_total,
                'subjects' => [
                    'toan' => $student->toan,
                    'vat_li' => $student->vat_li,
                    'hoa_hoc' => $student->hoa_hoc,
                ],
            ];
        }

        return $result;
    }

    /**
     * Get score report by 4 levels for a specific subject
     */
    public function getScoreReportByLevels(string $subjectKey): ?array
    {
        if (!isset(self::SUBJECT_NAMES[$subjectKey])) {
            return null;
        }

        // Count students by levels
        $excellent = Student::where($subjectKey, '>=', 8)
            ->where($subjectKey, '<=', 10)
            ->count();

        $good = Student::where($subjectKey, '>=', 6)
            ->where($subjectKey, '<', 8)
            ->count();

        $average = Student::where($subjectKey, '>=', 4)
            ->where($subjectKey, '<', 6)
            ->count();

        $poor = Student::where($subjectKey, '>=', 0)
            ->where($subjectKey, '<', 4)
            ->count();

        $total = Student::whereNotNull($subjectKey)->count();

        $calculatePercentage = function ($count) use ($total) {
            if ($total === 0) return '0.00%';
            return number_format(($count / $total) * 100, 2) . '%';
        };

        return [
            'subject' => self::SUBJECT_NAMES[$subjectKey],
            'levels' => [
                'excellent' => [
                    'count' => $excellent,
                    'percentage' => $calculatePercentage($excellent),
                ],
                'good' => [
                    'count' => $good,
                    'percentage' => $calculatePercentage($good),
                ],
                'average' => [
                    'count' => $average,
                    'percentage' => $calculatePercentage($average),
                ],
                'poor' => [
                    'count' => $poor,
                    'percentage' => $calculatePercentage($poor),
                ],
            ],
            'total' => $total,
        ];
    }

    /**
     * Get statistics for a specific subject
     */
    public function getSubjectStatistics(string $subjectKey): ?array
    {
        if (!isset(self::SUBJECT_NAMES[$subjectKey])) {
            return null;
        }

        // Cache statistics for 1 hour (data doesn't change frequently)
        $cacheKey = "statistics_{$subjectKey}";
        
        return cache()->remember($cacheKey, 3600, function () use ($subjectKey) {
            // Get basic stats using aggregation
            $stats = Student::whereNotNull($subjectKey)
                ->selectRaw("
                    COUNT(*) as total,
                    AVG($subjectKey) as average,
                    MAX($subjectKey) as highest,
                    MIN($subjectKey) as lowest,
                    SUM(CASE WHEN $subjectKey >= 8 THEN 1 ELSE 0 END) as excellent,
                    SUM(CASE WHEN $subjectKey >= 6 AND $subjectKey < 8 THEN 1 ELSE 0 END) as good,
                    SUM(CASE WHEN $subjectKey >= 4 AND $subjectKey < 6 THEN 1 ELSE 0 END) as average_count,
                    SUM(CASE WHEN $subjectKey < 4 THEN 1 ELSE 0 END) as poor
                ")
                ->first();

            if (!$stats || $stats->total === 0) {
                return null;
            }

            // Calculate median efficiently using database query
            $total = (int) $stats->total;
            $medianPosition = floor($total / 2);
            
            $medianQuery = Student::whereNotNull($subjectKey)
                ->orderBy($subjectKey);
            
            if ($total % 2 === 0) {
                // Even count: average of two middle values
                $middleScores = $medianQuery
                    ->skip($medianPosition - 1)
                    ->take(2)
                    ->pluck($subjectKey);
                $median = $middleScores->avg();
            } else {
                // Odd count: middle value
                $median = $medianQuery
                    ->skip($medianPosition)
                    ->value($subjectKey);
            }

            return [
                'subject' => self::SUBJECT_NAMES[$subjectKey],
                'total' => $total,
                'average' => round($stats->average, 2),
                'highest' => (float) $stats->highest,
                'lowest' => (float) $stats->lowest,
                'median' => round($median, 2),
                'distribution' => [
                    'excellent' => (int) $stats->excellent,
                    'good' => (int) $stats->good,
                    'average' => (int) $stats->average_count,
                    'poor' => (int) $stats->poor,
                ],
            ];
        });
    }

    /**
     * Get top 10 students of group A (Math, Physics, Chemistry)
     */
    public function getTop10GroupA(): array
    {
        // Cache top 10 for 1 hour (rarely changes)
        return cache()->remember('top10_group_a', 3600, function () {
            $students = Student::whereNotNull('toan')
                ->whereNotNull('vat_li')
                ->whereNotNull('hoa_hoc')
                ->selectRaw('*, (toan + vat_li + hoa_hoc) as total')
                ->orderBy('total', 'DESC')
                ->limit(10)
                ->get();

            return $students->map(function ($student, $index) {
                return [
                    'rank' => $index + 1,
                    'sbd' => $student->sbd,
                    'total' => round($student->total, 1),
                    'scores' => [
                        'toan' => $student->toan,
                        'vat_li' => $student->vat_li,
                        'hoa_hoc' => $student->hoa_hoc,
                    ],
                ];
            })->toArray();
        });
    }

    /**
     * Get valid subjects
     */
    public static function getValidSubjects(): array
    {
        return array_keys(self::SUBJECT_NAMES);
    }

    /**
     * Check if subject is valid
     */
    public static function isValidSubject(string $subject): bool
    {
        return isset(self::SUBJECT_NAMES[$subject]);
    }
}
