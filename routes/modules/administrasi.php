<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DokumenAdministrasiController;

Route::middleware(['auth'])->prefix('administrasi')->name('administrasi.')->group(function () {

    // Rute spesifik per kategori (untuk sidebar)
    Route::get('/sop', [DokumenAdministrasiController::class, 'index'])->name('sop')
        ->middleware('menu:administrasi.sop.view')->defaults('kategori', 'sop');
    Route::get('/tata-tertib', [DokumenAdministrasiController::class, 'index'])->name('tata_tertib')
        ->middleware('menu:administrasi.tata_tertib.view')->defaults('kategori', 'tata-tertib');
    Route::get('/struktur', [DokumenAdministrasiController::class, 'index'])->name('struktur')
        ->middleware('menu:administrasi.struktur.view')->defaults('kategori', 'struktur');

    // Rute dinamis berdasarkan kategori
    Route::get('/{kategori}', [DokumenAdministrasiController::class, 'index'])->name('index')
        ->middleware('menu:administrasi.view');
    Route::get('/{kategori}/create', [DokumenAdministrasiController::class, 'create'])->name('create')
        ->middleware('menu:administrasi.create');
    Route::post('/{kategori}', [DokumenAdministrasiController::class, 'store'])->name('store')
        ->middleware('menu:administrasi.create');
    Route::get('/{kategori}/{dokuman}', [DokumenAdministrasiController::class, 'show'])->name('show')
        ->middleware('menu:administrasi.view');
    Route::get('/{kategori}/{dokuman}/edit', [DokumenAdministrasiController::class, 'edit'])->name('edit')
        ->middleware('menu:administrasi.edit');
    Route::put('/{kategori}/{dokuman}', [DokumenAdministrasiController::class, 'update'])->name('update')
        ->middleware('menu:administrasi.edit');
    Route::delete('/{kategori}/{dokuman}', [DokumenAdministrasiController::class, 'destroy'])->name('destroy')
        ->middleware('menu:administrasi.delete');

    // Custom route untuk download via Storage Private
    Route::get('/{kategori}/{dokuman}/download', [DokumenAdministrasiController::class, 'download'])->name('download')
        ->middleware('menu:administrasi.view');

});
