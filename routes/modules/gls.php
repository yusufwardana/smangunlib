<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GLSController;

Route::middleware(['auth'])->prefix('gls')->name('gls.')->group(function () {

    Route::get('/', [GLSController::class, 'dashboard'])->name('dashboard')
        ->middleware('menu:literasi.view');

    // Manajemen Program
    Route::get('/program', [GLSController::class, 'programIndex'])->name('program.index')
        ->middleware('menu:literasi.view');
    Route::get('/program/create', [GLSController::class, 'programCreate'])->name('program.create')
        ->middleware('menu:literasi.create');
    Route::post('/program', [GLSController::class, 'programStore'])->name('program.store')
        ->middleware('menu:literasi.create');
    Route::get('/program/{id}', [GLSController::class, 'programShow'])->name('program.show')
        ->middleware('menu:literasi.view');
    Route::get('/program/{id}/edit', [GLSController::class, 'programEdit'])->name('program.edit')
        ->middleware('menu:literasi.edit');
    Route::put('/program/{id}', [GLSController::class, 'programUpdate'])->name('program.update')
        ->middleware('menu:literasi.edit');

    // Dokumentasi
    Route::post('/program/{id}/dokumentasi', [GLSController::class, 'uploadDokumentasi'])->name('program.dokumentasi')
        ->middleware('menu:literasi.upload');

    // Jurnal & Verifikasi
    Route::get('/jurnal', [GLSController::class, 'jurnalIndex'])->name('jurnal.index')
        ->middleware('menu:literasi.view');
    Route::post('/jurnal/verify', [GLSController::class, 'verifikasiJurnal'])->name('jurnal.verify')
        ->middleware('menu:literasi.approve');

    // Ekspor
    Route::get('/export', [GLSController::class, 'exportExcel'])->name('export')
        ->middleware('menu:literasi.export_excel');

});