<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/test', function () {
        return response()->json([
            'message' => 'TeamBoard API v1 is working.',
        ]);
    });
});