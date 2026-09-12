<?php

use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')
    ->as('api.v1.')
    ->group(function () {
        Route::post('/auth/token', [AuthController::class, 'token'])
            ->name('auth.token');
    });

Route::prefix('v1')
    ->as('api.v1.')
    ->middleware('auth:sanctum', 'company.token', 'throttle:api', )
    ->group(function () {
        Route::get('/test', function () {
            return response()->json([
                'message' => 'TeamBoard API v1 is working.',
            ]);
        });

        Route::get('/company', [CompanyController::class, 'show'])
            ->name('company');

        Route::apiResource('projects', ProjectController::class);

        Route::get('/projects/{project}/tasks', [TaskController::class, 'index'])
            ->name('projects.tasks.index');

        Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])
            ->name('projects.tasks.store');

        Route::get('/tasks/{task}', [TaskController::class, 'show'])
            ->name('tasks.show');

        Route::put('/tasks/{task}', [TaskController::class, 'update'])
            ->name('tasks.update');

        Route::patch('/tasks/{task}', [TaskController::class, 'update']);

        Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])
            ->name('tasks.destroy');

    });