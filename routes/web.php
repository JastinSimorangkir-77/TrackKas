<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\TransactionsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/transaksi', [TransactionsController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/create', [TransactionsController::class, 'create'])->name('transaksi.create');
    Route::post('/transaksi', [TransactionsController::class, 'store'])->name('transaksi.store');
    Route::delete('/transaksi/{transaction}', [TransactionsController::class, 'destroy'])->name('transaksi.destroy');

    // Laporan tidak punya halaman sendiri: tombol unduhnya ada di halaman
    // Transaksi, dan angkanya hanya muncul di dalam berkas PDF.
    Route::get('/laporan/pdf', [ReportsController::class, 'pdf'])->name('laporan.pdf');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
