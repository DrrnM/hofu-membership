<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\Owner\RewardController;
use App\Http\Controllers\Owner\LaporanController;

// === AUTHENTICATION ===
Route::get('/', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::middleware(['auth'])->group(function () {

    // DASHBOARD OWNER 
    Route::get('/owner/dashboard', [DashboardController::class, 'indexOwner'])
        ->name('owner.dashboard');

    // DASHBOARD KASIR 
    Route::get('/kasir/dashboard', [DashboardController::class, 'indexKasir'])
        ->name('kasir.dashboard');

    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    Route::get('/members/{id}/edit', [MemberController::class, 'edit'])->name('members.edit');
    Route::put('/members/{id}', [MemberController::class, 'update'])->name('members.update');
    Route::delete('/members/{id}', [MemberController::class, 'destroy'])->name('members.destroy');
});

Route::prefix('owner')->group(function () {
    // Reward
    Route::get('/reward', [RewardController::class, 'index'])->name('owner.reward.index');
    Route::get('/reward/create', [RewardController::class, 'create'])->name('owner.reward.create');
    Route::post('/reward', [RewardController::class, 'store'])->name('owner.reward.store');
    Route::get('/reward/{id}/edit', [RewardController::class, 'edit'])->name('owner.reward.edit');
    Route::put('/reward/{id}', [RewardController::class, 'update'])->name('owner.reward.update');
    Route::delete('/reward/{id}', [RewardController::class, 'destroy'])->name('owner.reward.destroy');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('owner.laporan.index');
    Route::delete('/laporan/{id}', [LaporanController::class, 'destroy'])->name('owner.laporan.destroy');
});

Route::get('/check', function () {
    dd(Auth::user());
})->withoutMiddleware(['auth']);
