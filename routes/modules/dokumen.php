<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DokumenController;

Route::middleware(['auth'])->prefix('manajemen-dokumen')->name('dokumen.')->group(function () {

    Route::get('dokumen', [DokumenController::class, 'index'])->name('index')
        ->middleware('menu:dokumen.view');
    Route::get('dokumen/create', [DokumenController::class, 'create'])->name('create')
        ->middleware('menu:dokumen.create');
    Route::post('dokumen', [DokumenController::class, 'store'])->name('store')
        ->middleware('menu:dokumen.create');
    Route::get('dokumen/{dokuman}', [DokumenController::class, 'show'])->name('show')
        ->middleware('menu:dokumen.view');
    Route::get('dokumen/{dokuman}/edit', [DokumenController::class, 'edit'])->name('edit')
        ->middleware('menu:dokumen.edit');
    Route::put('dokumen/{dokuman}', [DokumenController::class, 'update'])->name('update')
        ->middleware('menu:dokumen.edit');

    // Fitur Tambahan
    Route::get('dokumen/{id}/download', [DokumenController::class, 'download'])->name('download')
        ->middleware('menu:dokumen.download');
    Route::get('dokumen/{id}/preview', [DokumenController::class, 'preview'])->name('preview')
        ->middleware('menu:dokumen.view');

});
