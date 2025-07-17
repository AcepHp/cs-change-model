<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Produksi\ProduksiDashboardController;
use App\Http\Controllers\Quality\QualityDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:produksi'])->group(function () {
    Route::get('/produksi/dashboard', [ProduksiDashboardController::class, 'index'])->name('produksi.dashboard');
});

Route::middleware(['auth', 'role:quality'])->group(function () {
    Route::get('/quality/dashboard', [QualityDashboardController::class, 'index'])->name('quality.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
