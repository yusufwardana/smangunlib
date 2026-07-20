<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\LandingContent;
use App\Models\LandingMenu;
use App\Models\Setting;
use Illuminate\Http\Request;
use Throwable;

/**
 * Landing Page publik.
 *
 * Halaman ini dapat diakses oleh SEMUA pengunjung (guest maupun user yang
 * sudah login dari role apa pun). Tidak menggunakan middleware `guest`, dan
 * tidak melakukan redirect otomatis ke dashboard walaupun user sudah login.
 */
class LandingController extends Controller
{
    public function __invoke(Request $request)
    {
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

        $menus = LandingMenu::where('is_active', true)
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        $contents = LandingContent::active()
            ->orderBy('sort_order')
            ->get()
            ->groupBy('type');

        return view('landing', compact('latestBooks', 'settings', 'menus', 'contents'));
    }
}
