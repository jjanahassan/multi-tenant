<?php

use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\ProjectController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->as('api.v1.')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/test', function () {
            return response()->json([
                'message' => 'TeamBoard API v1 is working.',
            ]);
        });

        Route::get('/company', [CompanyController::class, 'show'])
            ->name('company');

        Route::apiResource('projects', ProjectController::class);
    });