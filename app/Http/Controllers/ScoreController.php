<?php

namespace App\Http\Controllers;

use App\Services\ScoreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    protected ScoreService $scoreService;

    public function __construct(ScoreService $scoreService)
    {
        $this->scoreService = $scoreService;
    }

    /**
     * Check score by registration number (SBD)
     * GET /api/scores/check/{sbd}
     */
    public function checkScore(string $sbd): JsonResponse
    {
        try {
            if (empty($sbd)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration number (SBD) is required',
                ], 400);
            }

            $result = $this->scoreService->checkScoreByRegistrationNumber($sbd);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => "Student with SBD {$sbd} not found",
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get score report by 4 levels for a subject
     * GET /api/scores/report/{subject}
     */
    public function getScoreReport(string $subject): JsonResponse
    {
        try {
            if (empty($subject)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subject is required',
                ], 400);
            }

            if (!ScoreService::isValidSubject($subject)) {
                $validSubjects = implode(', ', ScoreService::getValidSubjects());
                return response()->json([
                    'success' => false,
                    'message' => "Invalid subject. Valid subjects: {$validSubjects}",
                ], 400);
            }

            $report = $this->scoreService->getScoreReportByLevels($subject);

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Report not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $report,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get statistics for a subject
     * GET /api/scores/statistics/{subject}
     */
    public function getStatistics(string $subject): JsonResponse
    {
        try {
            if (empty($subject)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subject is required',
                ], 400);
            }

            if (!ScoreService::isValidSubject($subject)) {
                $validSubjects = implode(', ', ScoreService::getValidSubjects());
                return response()->json([
                    'success' => false,
                    'message' => "Invalid subject. Valid subjects: {$validSubjects}",
                ], 400);
            }

            $statistics = $this->scoreService->getSubjectStatistics($subject);

            if (!$statistics) {
                return response()->json([
                    'success' => false,
                    'message' => 'Statistics not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $statistics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get top 10 students of group A (Math, Physics, Chemistry)
     * GET /api/scores/top10/group-a
     */
    public function getTop10GroupA(): JsonResponse
    {
        try {
            $top10 = $this->scoreService->getTop10GroupA();

            return response()->json([
                'success' => true,
                'data' => $top10,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
