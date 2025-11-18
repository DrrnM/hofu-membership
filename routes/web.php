<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\Owner\RewardController;
use App\Http\Controllers\Owner\LaporanController;
use App\Http\Controllers\PoinController;

Route::get('/', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    Route::get('/owner/dashboard', [DashboardController::class, 'indexOwner'])
        ->name('owner.dashboard');

    Route::get('/kasir/dashboard', [DashboardController::class, 'indexKasir'])
        ->name('kasir.dashboard');

    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    Route::get('/members/{id_member}', [MemberController::class, 'show'])->name('members.show');
    Route::get('/members/{id_member}/edit', [MemberController::class, 'edit'])->name('members.edit');
    Route::put('/members/{id_member}', [MemberController::class, 'update'])->name('members.update');
    Route::delete('/members/{id_member}', [MemberController::class, 'destroy'])->name('members.destroy');
});

Route::prefix('owner')->group(function () {

    Route::get('/reward', [RewardController::class, 'index'])->name('owner.reward.index');
    Route::get('/reward/create', [RewardController::class, 'create'])->name('owner.reward.create');
    Route::post('/reward', [RewardController::class, 'store'])->name('owner.reward.store');
    Route::get('/reward/{id}/edit', [RewardController::class, 'edit'])->name('owner.reward.edit');
    Route::get('/reward/{id}', [RewardController::class, 'show'])->name('owner.reward.show');
    Route::put('/reward/{id}', [RewardController::class, 'update'])->name('owner.reward.update');
    Route::delete('/reward/{id}', [RewardController::class, 'destroy'])->name('owner.reward.destroy');

   Route::get('/laporan', [LaporanController::class, 'index'])->name('owner.laporan.index');
    Route::get('/laporan/create', [LaporanController::class, 'create'])->name('owner.laporan.create');
    Route::post('/laporan', [LaporanController::class, 'store'])->name('owner.laporan.store');
    Route::get('/laporan/{id}/download', [LaporanController::class, 'download'])->name('owner.laporan.download');
    Route::delete('/laporan/{id}', [LaporanController::class, 'destroy'])->name('owner.laporan.destroy');
});

Route::resource('poins', PoinController::class)->only(['index', 'edit', 'update']);

Route::get('/check', function () {
    dd(Auth::user());
})->withoutMiddleware(['auth']);