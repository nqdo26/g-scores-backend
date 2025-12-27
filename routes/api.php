<?php

use App\Http\Controllers\ScoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * API Routes for Score Management
 */

// Health check
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now(),
    ]);
});

// Score routes
Route::prefix('scores')->group(function () {
    /**
     * @route   GET /api/scores/check/{sbd}
     * @desc    Check score by registration number
     * @access  Public
     */
    Route::get('/check/{sbd}', [ScoreController::class, 'checkScore']);

    /**
     * @route   GET /api/scores/report/{subject}
     * @desc    Get score report by 4 levels for a subject
     * @access  Public
     */
    Route::get('/report/{subject}', [ScoreController::class, 'getScoreReport']);

    /**
     * @route   GET /api/scores/statistics/{subject}
     * @desc    Get statistics for a subject
     * @access  Public
     */
    Route::get('/statistics/{subject}', [ScoreController::class, 'getStatistics']);

    /**
     * @route   GET /api/scores/top10/group-a
     * @desc    Get top 10 students of group A (Math, Physics, Chemistry)
     * @access  Public
     */
    Route::get('/top10/group-a', [ScoreController::class, 'getTop10GroupA']);
});
