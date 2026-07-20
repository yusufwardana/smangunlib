<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanController;

Route::middleware(['auth'])->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/', [LaporanController::class, 'index'])->name('index')
        ->middleware('menu:laporan.view');
    Route::get('/generate', [LaporanController::class, 'generate'])->name('generate')
        ->middleware('menu:laporan.export_pdf');
});
