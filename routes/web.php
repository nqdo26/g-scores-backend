<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'G-Scores API is running',
        'version' => '1.0',
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version()
    ]);
});
