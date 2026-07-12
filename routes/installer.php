<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstallerController;

Route::middleware(['installed'])->prefix('install')->name('installer.')->group(function () {
    Route::get('/', [InstallerController::class, 'index'])->name('welcome');
    Route::get('/requirements', [InstallerController::class, 'requirements'])->name('requirements');
    Route::get('/permissions', [InstallerController::class, 'permissions'])->name('permissions');
    
    Route::get('/database', [InstallerController::class, 'database'])->name('database');
    Route::post('/database/test', [InstallerController::class, 'databaseTest'])->name('database.test');
    
    Route::get('/app', [InstallerController::class, 'appConfig'])->name('app');
    Route::post('/app', [InstallerController::class, 'appConfigStore'])->name('app.store');
    
    Route::get('/process', [InstallerController::class, 'process'])->name('process');
    
    // AJAX Processing Routes
    Route::post('/process/env', [InstallerController::class, 'processEnv'])->name('process.env');
    Route::post('/process/key', [InstallerController::class, 'processKey'])->name('process.key');
    Route::post('/process/symlink', [InstallerController::class, 'processSymlink'])->name('process.symlink');
    Route::post('/process/migrate', [InstallerController::class, 'processMigrate'])->name('process.migrate');
    Route::post('/process/seed', [InstallerController::class, 'processSeed'])->name('process.seed');
    
    Route::get('/admin', [InstallerController::class, 'admin'])->name('admin');
    Route::post('/admin', [InstallerController::class, 'adminStore'])->name('admin.store');
    
    Route::get('/config', [InstallerController::class, 'initialConfig'])->name('config');
    Route::post('/config', [InstallerController::class, 'initialConfigStore'])->name('config.store');
    
    Route::get('/finish', [InstallerController::class, 'finish'])->name('finish');
});
