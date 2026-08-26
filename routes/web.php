<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BoardColumnController;
use App\Http\Controllers\CompanyController;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    
    // Switch Company Routes
    Route::get('/switch-company', function () {
        return view('switch-company');
    })->name('switch-company');

    Route::post('/switch-company', function () {
        // Scaffold: actual switching will be implemented in Task 2
        return back()->with('info', 'Switch Company feature is coming soon!');
    })->name('switch-company.switch');
});

Route::middleware('auth')->group(function () {
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
});

require __DIR__.'/settings.php';