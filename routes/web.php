<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BoardColumnController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    
    // Switch Company Routes
    Route::get('/switch-company', function () {
        return view('switch-company');
    })->name('switch-company');

    Route::post('/switch-company', function () {
        return back()->with('info', 'Switch Company feature is coming soon!');
    })->name('switch-company.switch');
});

Route::middleware('auth')->scopeBindings()->group(function () {
    Route::resource('projects', ProjectController::class);

    Route::get('/invitations/create', [InvitationController::class, 'create'])
        ->name('invitations.create');

    Route::post('/invitations', [InvitationController::class, 'store'])
        ->name('invitations.store');

    Route::post(
        '/projects/{project}/columns',
        [BoardColumnController::class, 'store']
    )->name('projects.columns.store');

    Route::put(
        '/projects/{project}/columns/{boardColumn}',
        [BoardColumnController::class, 'update']
    )->name('projects.columns.update');

    Route::delete(
        '/projects/{project}/columns/{boardColumn}',
        [BoardColumnController::class, 'destroy']
    )->name('projects.columns.destroy');

    Route::patch(
        '/projects/{project}/columns/{boardColumn}/reorder/{direction}',
        [BoardColumnController::class, 'reorder']
    )->name('projects.columns.reorder');

    Route::get(
        '/company/users',
        [CompanyController::class, 'users']
    )->name('company.users');

    Route::delete(
        '/company/{company}/users/{user}',
        [CompanyController::class, 'removeUser']
    )->name('company.users.destroy');

    Route::delete(
        '/company/{company}',
        [CompanyController::class, 'destroy']
    )->name('company.destroy');

    Route::post(
        '/projects/{project}/tasks',
        [TaskController::class, 'store']
    )->name('projects.tasks.store');

    Route::put(
        '/projects/{project}/tasks/{task}',
        [TaskController::class, 'update']
    )->name('projects.tasks.update');

    Route::delete(
        '/projects/{project}/tasks/{task}',
        [TaskController::class, 'destroy']
    )->name('projects.tasks.destroy');

    Route::patch(
        '/projects/{project}/tasks/{task}/move',
        [TaskController::class, 'move']
    )->name('projects.tasks.move');
    
    Route::post(
        '/tasks/{task}/comments',
        [CommentController::class, 'store']
    )->name('tasks.comments.store');

    Route::delete(
        '/comments/{comment}',
        [CommentController::class, 'destroy']
    )->name('comments.destroy');

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');
});

require __DIR__.'/settings.php';