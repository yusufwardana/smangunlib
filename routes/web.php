<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingContentController;
use App\Models\Buku;
use App\Models\LandingContent;
use App\Models\LandingMenu;
use App\Models\Setting;

require __DIR__.'/installer.php';

Route::get('/landing', function () {
    $latestBooks = collect();

    try {
        $latestBooks = Buku::with('kategori')
            ->latest()
            ->take(8)
            ->get();
    } catch (Throwable $e) {
        $latestBooks = collect();
    }

    $settings = Setting::query()->pluck('value', 'key')->toArray();
    $menus = LandingMenu::where('is_active', true)->whereNull('parent_id')->with('children')->orderBy('sort_order')->get();
    $contents = LandingContent::active()->orderBy('sort_order')->get()->groupBy('type');

    return view('landing', compact('latestBooks', 'settings', 'menus', 'contents'));
})->name('landing');
Route::redirect('/beranda', '/landing');

// Halaman publik detail berita
Route::get('/berita/{slug}', [LandingContentController::class, 'showNews'])->name('berita.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('landing');
    }

    return view('welcome');
});

// Modul Rute Lainnya
foreach (glob(__DIR__ . '/modules/*.php') as $filename) {
    require $filename;
}
