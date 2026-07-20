<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SystemInfoController;
use App\Http\Controllers\SystemUpdateController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\LandingContentController;
use App\Http\Controllers\LandingMenuController;
use App\Http\Controllers\MediaManagerController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\MenuPermissionController;

Route::middleware(['auth', 'role:super_admin'])->prefix('system')->name('system.')->group(function () {
    
    // System Info
    Route::get('/info', [SystemInfoController::class, 'index'])->name('info');

    /*
     |----------------------------------------------------------------------
     | Modul Pengaturan Hak Akses Menu (RBAC)
     |----------------------------------------------------------------------
     | Hanya Super Admin. Mengatur matriks pohon menu x aksi per role,
     | copy/reset permission, rebuild & clear cache, serta audit log.
     */
    Route::get('/permissions', [MenuPermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/audit', [MenuPermissionController::class, 'audit'])->name('permissions.audit');
    Route::post('/permissions/cache/rebuild', [MenuPermissionController::class, 'rebuild'])->name('permissions.rebuild');
    Route::post('/permissions/cache/clear', [MenuPermissionController::class, 'clearCache'])->name('permissions.clear-cache');
    Route::put('/permissions/{role}', [MenuPermissionController::class, 'update'])->name('permissions.update');
    Route::post('/permissions/{role}/copy', [MenuPermissionController::class, 'copy'])->name('permissions.copy');
    Route::post('/permissions/{role}/reset', [MenuPermissionController::class, 'reset'])->name('permissions.reset');
    
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

Route::middleware(['auth', 'role:super_admin|kepala_perpustakaan'])->prefix('system')->name('system.')->group(function () {
    Route::get('/settings', [SystemSettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SystemSettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/test-smtp', [SystemSettingsController::class, 'testSmtp'])->name('settings.test-smtp');
    Route::post('/settings/test-whatsapp', [SystemSettingsController::class, 'testWhatsapp'])->name('settings.test-whatsapp');

    Route::get('/contents/{type?}', [LandingContentController::class, 'index'])->name('contents.index');
    Route::post('/contents/upload-image', [LandingContentController::class, 'uploadImage'])->name('contents.upload-image');
    Route::get('/contents/{type}/create', [LandingContentController::class, 'create'])->name('contents.create');
    Route::post('/contents', [LandingContentController::class, 'store'])->name('contents.store');
    Route::get('/contents/item/{content}/edit', [LandingContentController::class, 'edit'])->name('contents.edit');
    Route::put('/contents/item/{content}', [LandingContentController::class, 'update'])->name('contents.update');
    Route::delete('/contents/item/{content}', [LandingContentController::class, 'destroy'])->name('contents.destroy');

    Route::get('/menus', [LandingMenuController::class, 'index'])->name('menus.index');
    Route::post('/menus', [LandingMenuController::class, 'store'])->name('menus.store');
    Route::put('/menus/{menu}', [LandingMenuController::class, 'update'])->name('menus.update');
    Route::delete('/menus/{menu}', [LandingMenuController::class, 'destroy'])->name('menus.destroy');

    Route::get('/media', [MediaManagerController::class, 'index'])->name('media.index');
    Route::post('/media', [MediaManagerController::class, 'store'])->name('media.store');
    Route::delete('/media/{media}', [MediaManagerController::class, 'destroy'])->name('media.destroy');

    // Theme Manager
    Route::get('/theme', [ThemeController::class, 'index'])->name('theme.index');
    Route::post('/theme', [ThemeController::class, 'update'])->name('theme.update');
    Route::post('/theme/preview', [ThemeController::class, 'preview'])->name('theme.preview');
    Route::post('/theme/reset', [ThemeController::class, 'reset'])->name('theme.reset');
    Route::get('/theme/export', [ThemeController::class, 'export'])->name('theme.export');
    Route::post('/theme/import', [ThemeController::class, 'import'])->name('theme.import');
});
