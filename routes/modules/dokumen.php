<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DokumenController;

Route::middleware(['auth', 'role:super_admin|kepala_perpustakaan|pustakawan'])->prefix('manajemen-dokumen')->name('dokumen.')->group(function () {
    
    Route::resource('dokumen', DokumenController::class)->except(['destroy'])->names([
        'index' => 'index',
        'create' => 'create',
        'store' => 'store',
        'show' => 'show',
        'edit' => 'edit',
        'update' => 'update',
    ]);
    
    // Fitur Tambahan
    Route::get('dokumen/{id}/download', [DokumenController::class, 'download'])->name('download');
    Route::get('dokumen/{id}/preview', [DokumenController::class, 'preview'])->name('preview');

});
