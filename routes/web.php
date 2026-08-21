<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProjectController;

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
});

require __DIR__.'/settings.php';