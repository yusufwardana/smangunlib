<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnggotaController;

Route::middleware(['auth'])->prefix('keanggotaan')->name('anggota.')->group(function () {

    // Rute Master Anggota — akses dikontrol oleh permission database (bukan hardcode role)
    Route::middleware('menu:anggota.create')->group(function () {
        Route::get('anggota/create', [AnggotaController::class, 'create'])->name('create');
        Route::post('anggota', [AnggotaController::class, 'store'])->name('store');
    });

    Route::middleware('menu:anggota.view')->group(function () {
        Route::get('anggota', [AnggotaController::class, 'index'])->name('index');
        Route::get('anggota/{anggota}', [AnggotaController::class, 'show'])->name('show');
    });

    Route::middleware('menu:anggota.edit')->group(function () {
        Route::get('anggota/{anggota}/edit', [AnggotaController::class, 'edit'])->name('edit');
        Route::put('anggota/{anggota}', [AnggotaController::class, 'update'])->name('update');
    });

    Route::delete('anggota/{anggota}', [AnggotaController::class, 'destroy'])
        ->middleware('menu:anggota.delete')
        ->name('destroy');

    // Fitur Tambahan
    Route::get('export-anggota', [AnggotaController::class, 'exportExcel'])->name('export')
        ->middleware('menu:anggota.export_excel');
    Route::post('print-kartu', [AnggotaController::class, 'printKartuMassal'])->name('print_kartu')
        ->middleware('menu:anggota.print');

});
