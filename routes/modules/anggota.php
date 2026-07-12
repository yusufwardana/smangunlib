<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnggotaController;

Route::middleware(['auth', 'role:super_admin|kepala_perpustakaan|pustakawan'])->prefix('keanggotaan')->name('anggota.')->group(function () {
    
    // Rute Master Anggota
    Route::resource('anggota', AnggotaController::class)->names([
        'index' => 'index',
        'create' => 'create',
        'store' => 'store',
        'show' => 'show',
        'edit' => 'edit',
        'update' => 'update',
        'destroy' => 'destroy',
    ]);
    
    // Fitur Tambahan
    Route::get('export-anggota', [AnggotaController::class, 'exportExcel'])->name('export');
    Route::post('print-kartu', [AnggotaController::class, 'printKartuMassal'])->name('print_kartu');

});
