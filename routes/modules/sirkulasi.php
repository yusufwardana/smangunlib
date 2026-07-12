<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SirkulasiController;

Route::middleware(['auth', 'role:super_admin|kepala_perpustakaan|pustakawan'])->prefix('sirkulasi')->name('sirkulasi.')->group(function () {
    
    // Dashboard / Riwayat Transaksi
    Route::get('/', [SirkulasiController::class, 'index'])->name('index');
    
    // Peminjaman
    Route::get('/peminjaman', [SirkulasiController::class, 'peminjamanForm'])->name('peminjaman.form');
    Route::post('/peminjaman', [SirkulasiController::class, 'storePeminjaman'])->name('peminjaman.store');
    
    // Pengembalian
    Route::get('/pengembalian/{nomor_transaksi?}', [SirkulasiController::class, 'pengembalianForm'])->name('pengembalian.form');
    Route::post('/pengembalian/{id}', [SirkulasiController::class, 'prosesPengembalian'])->name('pengembalian.proses');
    
    // Perpanjangan
    Route::post('/perpanjang/{id}', [SirkulasiController::class, 'perpanjang'])->name('perpanjang');
    
    // Struk Bukti
    Route::get('/cetak-struk/{id}', [SirkulasiController::class, 'cetakStruk'])->name('cetak_struk');

});
