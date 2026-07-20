<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Koleksi\BukuController;

Route::middleware(['auth'])->prefix('koleksi')->name('koleksi.')->group(function () {
    
    // Rute Master Buku — akses dikontrol oleh permission database (bukan hardcode role)
    // PENTING: route literal (create, export) harus didaftarkan SEBELUM route wildcard {buku}

    Route::middleware('menu:koleksi.buku.create')->group(function () {
        Route::get('buku/create', [BukuController::class, 'create'])->name('buku.create');
        Route::post('buku', [BukuController::class, 'store'])->name('buku.store');
    });

    Route::middleware('menu:koleksi.buku.view')->group(function () {
        Route::get('buku', [BukuController::class, 'index'])->name('buku.index');
        Route::get('buku/{buku}', [BukuController::class, 'show'])->name('buku.show');
    });

    Route::middleware('menu:koleksi.buku.edit')->group(function () {
        Route::get('buku/{buku}/edit', [BukuController::class, 'edit'])->name('buku.edit');
        Route::put('buku/{buku}', [BukuController::class, 'update'])->name('buku.update');
    });

    Route::delete('buku/{buku}', [BukuController::class, 'destroy'])
        ->middleware('menu:koleksi.buku.delete')
        ->name('buku.destroy');
    
    // Fitur Tambahan Buku
    Route::get('buku-export', [BukuController::class, 'exportExcel'])
        ->middleware('menu:koleksi.buku.export_excel')
        ->name('buku.export');
    Route::post('buku-print-barcode', [BukuController::class, 'printBarcodeMassal'])
        ->middleware('menu:koleksi.buku.print')
        ->name('buku.print_barcode');

    // Nanti ditambahkan route Kategori, Rak, Eksemplar dll
});
