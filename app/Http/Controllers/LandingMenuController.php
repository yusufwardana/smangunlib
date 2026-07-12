<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLandingMenuRequest;
use App\Models\LandingMenu;
use App\Services\ActivityLogger;

class LandingMenuController extends Controller
{
    public function index()
    {
        return view('system.menus.index', [
            'menus' => LandingMenu::with('parent')->orderBy('sort_order')->paginate(20),
            'parents' => LandingMenu::whereNull('parent_id')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(StoreLandingMenuRequest $request)
    {
        $menu = LandingMenu::create($request->validated() + ['is_active' => $request->boolean('is_active')]);
        ActivityLogger::log('create', LandingMenu::class, $menu->id, null, $menu);

        return back()->with('success', 'Menu berhasil ditambahkan.');
    }

    public function update(StoreLandingMenuRequest $request, LandingMenu $menu)
    {
        $before = $menu->replicate();
        $menu->update($request->validated() + ['is_active' => $request->boolean('is_active')]);
        ActivityLogger::log('update', LandingMenu::class, $menu->id, $before, $menu);

        return back()->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(LandingMenu $menu)
    {
        $before = $menu->replicate();
        $menu->delete();
        ActivityLogger::log('delete', LandingMenu::class, $menu->id, $before, null);

        return back()->with('success', 'Menu berhasil dihapus.');
    }
}
