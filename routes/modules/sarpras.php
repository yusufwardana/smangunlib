<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlaceholderController;

Route::middleware(['auth'])->prefix('sarpras')->name('sarpras.')->group(function () {
    Route::get('/', [PlaceholderController::class, 'index'])->name('index')
        ->middleware('menu:sarpras.view');
});