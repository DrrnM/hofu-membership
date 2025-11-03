<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// ======== ROUTE LOGIN ==========
Route::get('/', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ======== ROUTE DASHBOARD (KASIR & OWNER) ==========
Route::middleware(['auth'])->group(function () {

    // Dashboard Kasir
    Route::get('resources/views/Kasir/Dashboard.blade.php', function () {
        return view('kasir.dashboard');
    })->middleware('role:kasir')->name('kasir.dashboard');

    // Dashboard Owner
    Route::get('resources\views\Owner\Dashboard.blade.php', function () {
        return view('owner.dashboard');
    })->middleware('role:owner')->name('owner.dashboard');
});
