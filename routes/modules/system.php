<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SystemInfoController;
use App\Http\Controllers\SystemUpdateController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\LicenseController;

Route::middleware(['auth', 'role:super_admin'])->prefix('system')->name('system.')->group(function () {
    
    // System Info
    Route::get('/info', [SystemInfoController::class, 'index'])->name('info');
    
    // License
    Route::get('/license', [LicenseController::class, 'index'])->name('license');
    Route::post('/license/activate', [LicenseController::class, 'activate'])->name('license.activate');
    
    // Backup
    Route::get('/backup', [BackupController::class, 'index'])->name('backup');
    Route::post('/backup/process', [BackupController::class, 'process'])->name('backup.process');
    Route::get('/backup/download/{id}', [BackupController::class, 'download'])->name('backup.download');
    Route::delete('/backup/{id}', [BackupController::class, 'destroy'])->name('backup.destroy');
    
    // Update
    Route::get('/update', [SystemUpdateController::class, 'index'])->name('update');
    Route::post('/update/upload', [SystemUpdateController::class, 'upload'])->name('update.upload');
});
