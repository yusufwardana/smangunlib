<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LandingContentController;

require __DIR__.'/installer.php';

/*
|--------------------------------------------------------------------------
| Landing Page (Publik)
|--------------------------------------------------------------------------
| Dapat diakses oleh semua pengunjung: Guest, Siswa, Guru, Pustakawan,
| Kepala Perpustakaan, Kepala Sekolah, dan Super Admin. Tidak menggunakan
| middleware `guest` dan tidak melakukan redirect otomatis ke dashboard.
*/
Route::get('/', LandingController::class)->name('landing');
Route::get('/home', LandingController::class)->name('home');
Route::get('/beranda', LandingController::class)->name('beranda');

// Halaman publik detail berita
Route::get('/berita/{slug}', [LandingContentController::class, 'showNews'])->name('berita.show');

/*
|--------------------------------------------------------------------------
| Autentikasi
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Dashboard (Terlindungi)
|--------------------------------------------------------------------------
| Hanya user yang sudah login yang dapat mengakses dashboard. Landing page
| tetap dapat dibuka kembali tanpa dipaksa kembali ke dashboard.
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => view('welcome'))->name('dashboard');
});

// Modul Rute Lainnya
foreach (glob(__DIR__ . '/modules/*.php') as $filename) {
    require $filename;
}
