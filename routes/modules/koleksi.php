<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Koleksi\BukuController;
use App\Http\Controllers\PlaceholderController;

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

    // Placeholder routes — fitur dalam pengembangan
    Route::get('kategori', [PlaceholderController::class, 'index'])->name('kategori.index')->middleware('menu:koleksi.kategori.view');
    Route::get('rak', [PlaceholderController::class, 'index'])->name('rak.index')->middleware('menu:koleksi.rak.view');
    Route::get('penulis', [PlaceholderController::class, 'index'])->name('penulis.index')->middleware('menu:koleksi.penulis.view');
    Route::get('penerbit', [PlaceholderController::class, 'index'])->name('penerbit.index')->middleware('menu:koleksi.penerbit.view');
    Route::get('inventaris', [PlaceholderController::class, 'index'])->name('inventaris.index')->middleware('menu:koleksi.inventaris.view');
});
