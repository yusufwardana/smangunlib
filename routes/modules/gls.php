<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GLSController;

Route::middleware(['auth', 'role:super_admin|kepala_perpustakaan|pustakawan|guru'])->prefix('gls')->name('gls.')->group(function () {
    
    Route::get('/', [GLSController::class, 'dashboard'])->name('dashboard');
    
    // Manajemen Program
    Route::get('/program', [GLSController::class, 'programIndex'])->name('program.index');
    Route::get('/program/create', [GLSController::class, 'programCreate'])->name('program.create');
    Route::post('/program', [GLSController::class, 'programStore'])->name('program.store');
    Route::get('/program/{id}', [GLSController::class, 'programShow'])->name('program.show');
    Route::get('/program/{id}/edit', [GLSController::class, 'programEdit'])->name('program.edit');
    Route::put('/program/{id}', [GLSController::class, 'programUpdate'])->name('program.update');
    
    // Dokumentasi
    Route::post('/program/{id}/dokumentasi', [GLSController::class, 'uploadDokumentasi'])->name('program.dokumentasi');
    
    // Jurnal & Verifikasi
    Route::get('/jurnal', [GLSController::class, 'jurnalIndex'])->name('jurnal.index');
    Route::post('/jurnal/verify', [GLSController::class, 'verifikasiJurnal'])->name('jurnal.verify');
    
    // Ekspor
    Route::get('/export', [GLSController::class, 'exportExcel'])->name('export');

});
