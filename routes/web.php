<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\Owner\RewardController;
use App\Http\Controllers\Owner\LaporanController;
use App\Http\Controllers\PoinController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\OwnerController;

Route::get('/', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/owner/dashboard', [OwnerController::class, 'dashboard'])
        ->middleware('owner')
        ->name('owner.dashboard');

    Route::get('/kasir/dashboard', [KasirController::class, 'dashboard'])
        ->middleware('kasir')
        ->name('kasir.dashboard');

    Route::get('/kasir/transaksi', [KasirController::class, 'historyPoin']) // atau buat method khusus
        ->name('kasir.transaksi');

    Route::get('/dashoard/chart-data', [DashboardController::class, 'getChartDataApi']);

    Route::get('/owner/chart-data', [OwnerController::class, 'chartData'])
        ->middleware('owner')
        ->name('owner.chart.data');


    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
    Route::post('/members/store', [MemberController::class, 'store'])->name('members.store');
    Route::get('/members/{id_member}', [MemberController::class, 'show'])->name('members.show');
    Route::get('/members/{id_member}/edit', [MemberController::class, 'edit'])->name('members.edit');
    Route::put('/members/{id_member}', [MemberController::class, 'update'])->name('members.update');
    Route::delete('/members/{id_member}', [MemberController::class, 'destroy'])->name('members.destroy');

    Route::get('/poins', [PoinController::class, 'index'])->name('poins.index');
    Route::get('/poins/create', [PoinController::class, 'create'])->name('poins.create');
    Route::post('/poins', [PoinController::class, 'store'])->name('poins.store');
    Route::get('/poins/history', [PoinController::class, 'history'])->name('poins.history');

    Route::post('/members/{id_member}/tambah-poin', [PoinController::class, 'tambahPoin'])
        ->name('members.tambah-poin');

    Route::get('/transactions', [TransaksiController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create', [TransaksiController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransaksiController::class, 'store'])->name('transactions.store');
});

Route::prefix('owner')->middleware(['auth', 'owner'])->group(function () {
    Route::get('/reward', [RewardController::class, 'index'])->name('owner.reward.index');
    Route::get('/reward/create', [RewardController::class, 'create'])->name('owner.reward.create');
    Route::post('/reward', [RewardController::class, 'store'])->name('owner.reward.store');
    Route::get('/reward/{id_reward}/edit', [RewardController::class, 'edit'])->name('owner.reward.edit');
    Route::get('/reward/{id_reward}', [RewardController::class, 'show'])->name('owner.reward.show');
    Route::put('/reward/{id_reward}', [RewardController::class, 'update'])->name('owner.reward.update');
    Route::delete('/reward/{id_reward}', [RewardController::class, 'destroy'])->name('owner.reward.destroy');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('owner.laporan.index');
    Route::get('/laporan/create', [LaporanController::class, 'create'])->name('owner.laporan.create');
    Route::post('/laporan', [LaporanController::class, 'store'])->name('owner.laporan.store');
    Route::get('/laporan/{id}/download', [LaporanController::class, 'download'])->name('owner.laporan.download');
    Route::get('/laporan/{id}/view', [LaporanController::class, 'view'])->name('owner.laporan.view');
    Route::get('/laporan/scan', [LaporanController::class, 'scanExistingFiles'])->name('owner.laporan.scan');
    Route::delete('/laporan/{id}', [LaporanController::class, 'destroy'])->name('owner.laporan.destroy');


    Route::get('/poins/{id_member}/edit', [PoinController::class, 'edit'])->name('poins.edit');
    Route::put('/poins/{id_member}', [PoinController::class, 'update'])->name('poins.update');
    Route::get('/members/{id_member}/update-poin', [PoinController::class, 'updatePoinForm'])->name('members.update-poin');
    Route::post('/members/{id_member}/update-poin', [PoinController::class, 'updatePoin'])->name('members.update-poin.store');
});

Route::prefix('kasir')->middleware(['auth', 'kasir'])->group(function () {
    Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('kasir.dashboard');
    Route::get('/quick-poin', [PoinController::class, 'quickInput'])->name('kasir.quick-poin');
    Route::post('/process-poin', [PoinController::class, 'processQuickPoin'])->name('kasir.process-poin');
});

Route::get('/check', function () {
    dd(Auth::user());
})->withoutMiddleware(['auth']);
Route::get('/test-timezone', function () {
    echo "1. Config timezone: " . config('app.timezone') . "<br>";
    echo "2. PHP timezone: " . date_default_timezone_get() . "<br>";
    echo "3. Laravel now(): " . now()->format('Y-m-d H:i:s') . "<br>";
    echo "4. PHP date(): " . date('Y-m-d H:i:s') . "<br>";
    echo "5. Carbon now(): " . \Carbon\Carbon::now()->format('Y-m-d H:i:s') . "<br>";
    echo "6. Carbon Jakarta: " . \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s') . "<br>";

    $transaksi = \App\Models\Transaksi::latest()->first();
    if ($transaksi) {
        echo "<br>Transaksi terbaru:<br>";
        echo "- Created at: " . $transaksi->created_at . "<br>";
        echo "- Format lokal: " . $transaksi->created_at->timezone('Asia/Jakarta')->format('Y-m-d H:i:s');
    }
});