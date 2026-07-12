<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DokumenAdministrasiController;

Route::middleware(['auth', 'role:super_admin|kepala_perpustakaan'])->prefix('administrasi')->name('administrasi.')->group(function () {
    
    // Rute dinamis berdasarkan kategori
    Route::get('/{kategori}', [DokumenAdministrasiController::class, 'index'])->name('index');
    Route::get('/{kategori}/create', [DokumenAdministrasiController::class, 'create'])->name('create');
    Route::post('/{kategori}', [DokumenAdministrasiController::class, 'store'])->name('store');
    Route::get('/{kategori}/{dokuman}', [DokumenAdministrasiController::class, 'show'])->name('show');
    Route::get('/{kategori}/{dokuman}/edit', [DokumenAdministrasiController::class, 'edit'])->name('edit');
    Route::put('/{kategori}/{dokuman}', [DokumenAdministrasiController::class, 'update'])->name('update');
    Route::delete('/{kategori}/{dokuman}', [DokumenAdministrasiController::class, 'destroy'])->name('destroy');
    
    // Custom route untuk download via Storage Private
    Route::get('/{kategori}/{dokuman}/download', [DokumenAdministrasiController::class, 'download'])->name('download');

});
