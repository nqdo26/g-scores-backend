<?php

use App\Http\Controllers\ScoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now(),
    ]);
});

Route::prefix('scores')->group(function () {
    Route::get('/checkScore', [ScoreController::class, 'checkScore']);

    Route::get('/report/{subject}', [ScoreController::class, 'getScoreReport']);

    Route::get('/statistics/{subject}', [ScoreController::class, 'getStatistics']);

    Route::get('/top10/group-a', [ScoreController::class, 'getTop10GroupA']);
});
