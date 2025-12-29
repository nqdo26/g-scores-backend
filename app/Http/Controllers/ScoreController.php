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

    public function checkScore(Request $request): JsonResponse
    {
        try {
            $uid = $request->query('uid');

            if (empty($uid)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration number (uid) is required',
                ], 400);
            }

            if (!is_numeric($uid)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration number (uid) must be numeric',
                ], 400);
            }

            $result = $this->scoreService->checkScoreByRegistrationNumber($uid);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => "Student with uid {$uid} not found",
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
