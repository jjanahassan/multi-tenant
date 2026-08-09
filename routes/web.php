<?php

use Illuminate\Support\Facades\Route;

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

require __DIR__.'/settings.php';