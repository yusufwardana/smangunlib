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
use App\Http\Controllers\PlaceholderController;

Route::middleware(['auth'])->prefix('system')->name('system.')->group(function () {

    // System Info
    Route::get('/info', [SystemInfoController::class, 'index'])->name('info')
        ->middleware('menu:sistem.info');

    // RBAC Permission Management (Super Admin)
    Route::get('/permissions', [MenuPermissionController::class, 'index'])->name('permissions.index')
        ->middleware('menu:sistem.permissions.index');
    Route::get('/permissions/audit', [MenuPermissionController::class, 'audit'])->name('permissions.audit')
        ->middleware('menu:sistem.permissions.audit');
    Route::post('/permissions/cache/rebuild', [MenuPermissionController::class, 'rebuild'])->name('permissions.rebuild')
        ->middleware('menu:sistem.permissions.rebuild');
    Route::post('/permissions/cache/clear', [MenuPermissionController::class, 'clearCache'])->name('permissions.clear-cache')
        ->middleware('menu:sistem.permissions.clear-cache');
    Route::put('/permissions/{role}', [MenuPermissionController::class, 'update'])->name('permissions.update')
        ->middleware('menu:sistem.permissions.update');
    Route::post('/permissions/{role}/copy', [MenuPermissionController::class, 'copy'])->name('permissions.copy')
        ->middleware('menu:sistem.permissions.copy');
    Route::post('/permissions/{role}/reset', [MenuPermissionController::class, 'reset'])->name('permissions.reset')
        ->middleware('menu:sistem.permissions.reset');

    // License
    Route::get('/license', [LicenseController::class, 'index'])->name('license')
        ->middleware('menu:sistem.license');
    Route::post('/license/activate', [LicenseController::class, 'activate'])->name('license.activate')
        ->middleware('menu:sistem.license.activate');

    // Backup
    Route::get('/backup', [BackupController::class, 'index'])->name('backup')
        ->middleware('menu:sistem.backup');
    Route::post('/backup/process', [BackupController::class, 'process'])->name('backup.process')
        ->middleware('menu:sistem.backup.process');
    Route::get('/backup/download/{id}', [BackupController::class, 'download'])->name('backup.download')
        ->middleware('menu:sistem.backup.download');
    Route::delete('/backup/{id}', [BackupController::class, 'destroy'])->name('backup.destroy')
        ->middleware('menu:sistem.backup.destroy');

    // Update
    Route::get('/update', [SystemUpdateController::class, 'index'])->name('update')
        ->middleware('menu:sistem.update');
    Route::post('/update/upload', [SystemUpdateController::class, 'upload'])->name('update.upload')
        ->middleware('menu:sistem.update.upload');
});

Route::middleware(['auth'])->prefix('system')->name('system.')->group(function () {

    Route::get('/settings', [SystemSettingsController::class, 'index'])->name('settings.index')
        ->middleware('menu:sistem.settings.index');
    Route::post('/settings', [SystemSettingsController::class, 'update'])->name('settings.update')
        ->middleware('menu:sistem.settings.update');
    Route::post('/settings/test-smtp', [SystemSettingsController::class, 'testSmtp'])->name('settings.test-smtp')
        ->middleware('menu:sistem.settings.test-smtp');
    Route::post('/settings/test-whatsapp', [SystemSettingsController::class, 'testWhatsapp'])->name('settings.test-whatsapp')
        ->middleware('menu:sistem.settings.test-whatsapp');

    Route::get('/contents/{type?}', [LandingContentController::class, 'index'])->name('contents.index')
        ->middleware('menu:sistem.contents.index');
    Route::post('/contents/upload-image', [LandingContentController::class, 'uploadImage'])->name('contents.upload-image')
        ->middleware('menu:sistem.contents.upload-image');
    Route::get('/contents/{type}/create', [LandingContentController::class, 'create'])->name('contents.create')
        ->middleware('menu:sistem.contents.create');
    Route::post('/contents', [LandingContentController::class, 'store'])->name('contents.store')
        ->middleware('menu:sistem.contents.store');
    Route::get('/contents/item/{content}/edit', [LandingContentController::class, 'edit'])->name('contents.edit')
        ->middleware('menu:sistem.contents.edit');
    Route::put('/contents/item/{content}', [LandingContentController::class, 'update'])->name('contents.update')
        ->middleware('menu:sistem.contents.update');
    Route::delete('/contents/item/{content}', [LandingContentController::class, 'destroy'])->name('contents.destroy')
        ->middleware('menu:sistem.contents.destroy');

    Route::get('/menus', [LandingMenuController::class, 'index'])->name('menus.index')
        ->middleware('menu:sistem.menus.index');
    Route::post('/menus', [LandingMenuController::class, 'store'])->name('menus.store')
        ->middleware('menu:sistem.menus.store');
    Route::put('/menus/{menu}', [LandingMenuController::class, 'update'])->name('menus.update')
        ->middleware('menu:sistem.menus.update');
    Route::delete('/menus/{menu}', [LandingMenuController::class, 'destroy'])->name('menus.destroy')
        ->middleware('menu:sistem.menus.destroy');

    Route::get('/media', [MediaManagerController::class, 'index'])->name('media.index')
        ->middleware('menu:sistem.media.index');
    Route::post('/media', [MediaManagerController::class, 'store'])->name('media.store')
        ->middleware('menu:sistem.media.store');
    Route::delete('/media/{media}', [MediaManagerController::class, 'destroy'])->name('media.destroy')
        ->middleware('menu:sistem.media.destroy');

    // Theme Manager
    Route::get('/theme', [ThemeController::class, 'index'])->name('theme.index')
        ->middleware('menu:sistem.theme.index');
    Route::post('/theme', [ThemeController::class, 'update'])->name('theme.update')
        ->middleware('menu:sistem.theme.update');
    Route::post('/theme/preview', [ThemeController::class, 'preview'])->name('theme.preview')
        ->middleware('menu:sistem.theme.preview');
    Route::post('/theme/reset', [ThemeController::class, 'reset'])->name('theme.reset')
        ->middleware('menu:sistem.theme.reset');
    Route::get('/theme/export', [ThemeController::class, 'export'])->name('theme.export')
        ->middleware('menu:sistem.theme.export');
    Route::post('/theme/import', [ThemeController::class, 'import'])->name('theme.import')
        ->middleware('menu:sistem.theme.import');

    // Placeholder routes — fitur dalam pengembangan
    Route::get('/users', [PlaceholderController::class, 'index'])->name('users.index')
        ->middleware('menu:sistem.user.view');
    Route::get('/roles', [PlaceholderController::class, 'index'])->name('roles.index')
        ->middleware('menu:sistem.role.view');
    Route::get('/restore', [PlaceholderController::class, 'index'])->name('restore.index')
        ->middleware('menu:sistem.restore.view');
    Route::get('/activity-log', [PlaceholderController::class, 'index'])->name('activity-log.index')
        ->middleware('menu:sistem.activity_log.view');
});