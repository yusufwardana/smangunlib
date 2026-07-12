<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

require __DIR__.'/installer.php';

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return view('welcome');
});

// Modul Rute Lainnya
foreach (glob(__DIR__ . '/modules/*.php') as $filename) {
    require $filename;
}
