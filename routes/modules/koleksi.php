<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Koleksi\BukuController;

Route::middleware(['auth', 'role:super_admin|kepala_perpustakaan|pustakawan'])->prefix('koleksi')->name('koleksi.')->group(function () {
    
    // Rute Master Buku
    Route::resource('buku', BukuController::class);
    
    // Fitur Tambahan Buku
    Route::get('buku-export', [BukuController::class, 'exportExcel'])->name('buku.export');
    Route::post('buku-print-barcode', [BukuController::class, 'printBarcodeMassal'])->name('buku.print_barcode');

    // Nanti ditambahkan route Kategori, Rak, Eksemplar dll
});
