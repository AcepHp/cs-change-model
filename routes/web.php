<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ManagementUserController;
use App\Http\Controllers\Produksi\ProduksiDashboardController;
use App\Http\Controllers\Quality\QualityDashboardController;
use App\Http\Controllers\ChangeModelController;
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
    // Data User
    Route::get('/management-user', [ManagementUserController::class, 'index'])->name('managementUser');
    Route::get('/management-user/create', [ManagementUserController::class, 'create'])->name('user.create');
    Route::post('/management-user', [ManagementUserController::class, 'store'])->name('user.store');
    Route::get('/management-user/{id}/edit', [ManagementUserController::class, 'edit'])->name('user.edit');
    Route::put('/management-user/{id}', [ManagementUserController::class, 'update'])->name('user.update');
    Route::delete('/management-user/{id}', [ManagementUserController::class, 'destroy'])->name('user.destroy');
    // Manage Data Master
    Route::get('/data-master', [ChangeModelController::class, 'index'])->name('dataMaster.index');
    Route::get('/data-master/create', [ChangeModelController::class, 'create'])->name('dataMaster.create');
    Route::post('/data-master', [ChangeModelController::class, 'store'])->name('dataMaster.store');
    Route::get('/data-master/{id}', [ChangeModelController::class, 'show'])->name('dataMaster.show');
    Route::get('/data-master/{id}/edit', [ChangeModelController::class, 'edit'])->name('dataMaster.edit');
    Route::put('/data-master/{id}', [ChangeModelController::class, 'update'])->name('dataMaster.update');
    Route::delete('/data-master/{id}', [ChangeModelController::class, 'destroy'])->name('dataMaster.destroy');
});

require __DIR__.'/auth.php';
