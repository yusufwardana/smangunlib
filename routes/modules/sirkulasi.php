<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SirkulasiController;
use App\Http\Controllers\PlaceholderController;

Route::middleware(['auth'])->prefix('sirkulasi')->name('sirkulasi.')->group(function () {

    // Dashboard / Riwayat Transaksi
    Route::get('/', [SirkulasiController::class, 'index'])->name('index')
        ->middleware('menu:sirkulasi.view');

    // Peminjaman
    Route::get('/peminjaman', [SirkulasiController::class, 'peminjamanForm'])->name('peminjaman.form')
        ->middleware('menu:sirkulasi.peminjaman.view');
    Route::post('/peminjaman', [SirkulasiController::class, 'storePeminjaman'])->name('peminjaman.store')
        ->middleware('menu:sirkulasi.peminjaman.create');

    // Pengembalian
    Route::get('/pengembalian/{nomor_transaksi?}', [SirkulasiController::class, 'pengembalianForm'])->name('pengembalian.form')
        ->middleware('menu:sirkulasi.pengembalian.view');
    Route::post('/pengembalian/{id}', [SirkulasiController::class, 'prosesPengembalian'])->name('pengembalian.proses')
        ->middleware('menu:sirkulasi.pengembalian.approve');

    // Perpanjangan
    Route::post('/perpanjang/{id}', [SirkulasiController::class, 'perpanjang'])->name('perpanjang')
        ->middleware('menu:sirkulasi.peminjaman.approve');

    // Struk Bukti
    Route::get('/cetak-struk/{id}', [SirkulasiController::class, 'cetakStruk'])->name('cetak_struk')
        ->middleware('menu:sirkulasi.peminjaman.print');

    // Placeholder routes — fitur dalam pengembangan
    Route::get('/reservasi', [PlaceholderController::class, 'index'])->name('reservasi.index')->middleware('menu:sirkulasi.reservasi.view');
    Route::get('/denda', [PlaceholderController::class, 'index'])->name('denda.index')->middleware('menu:sirkulasi.denda.view');
    Route::get('/pengunjung', [PlaceholderController::class, 'index'])->name('pengunjung.index')->middleware('menu:sirkulasi.pengunjung.view');
});
