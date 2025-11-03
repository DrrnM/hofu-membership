<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;

Route::get('/', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::middleware(['auth'])->group(function () {
    Route::get('/kasir/dashboard', fn() => view('kasir.dashboard'));
    Route::get('/owner/dashboard', fn() => view('owner.dashboard'));
});

Route::get('/check', function () {
    dd(Auth::user());
});