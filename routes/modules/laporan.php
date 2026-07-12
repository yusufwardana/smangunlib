<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanController;

Route::middleware(['auth', 'role:super_admin|kepala_perpustakaan|kepala_sekolah'])->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/', [LaporanController::class, 'index'])->name('index');
    Route::get('/generate', [LaporanController::class, 'generate'])->name('generate');
});
