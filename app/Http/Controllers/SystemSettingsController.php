<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSystemSettingsRequest;
use App\Models\LandingContent;
use App\Models\LandingMenu;
use App\Models\MediaAsset;
use App\Models\Setting;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SystemSettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::query()->pluck('value', 'key')->toArray();
        $contentCounts = LandingContent::selectRaw('type, COUNT(*) as total')->groupBy('type')->pluck('total', 'type');
        $menuCount = LandingMenu::count();
        $mediaCount = MediaAsset::count();

        return view('system.settings.index', compact('settings', 'contentCounts', 'menuCount', 'mediaCount'));
    }

    public function update(UpdateSystemSettingsRequest $request)
    {
        $group = $request->string('group')->toString();
        $before = Setting::query()->where('key', 'like', $group.'.%')->pluck('value', 'key')->toArray();

        foreach ($request->input('settings', []) as $key => $value) {
            Setting::set($group.'.'.$key, is_bool($value) ? (int) $value : $value);
        }

        foreach ($request->file('uploads', []) as $key => $file) {
            if (! $file) {
                continue;
            }

            $path = $file->store('settings/'.$group, 'public');
            Setting::set($group.'.'.$key, $path);
        }

        $after = Setting::query()->where('key', 'like', $group.'.%')->pluck('value', 'key')->toArray();
        ActivityLogger::log('update_settings', 'settings:'.$group, 0, $before, $after);

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function testSmtp(Request $request)
    {
        ActivityLogger::log('test_smtp', 'settings:smtp');

        return back()->with('success', 'Konfigurasi SMTP tersimpan. Pengiriman email uji dapat diaktifkan setelah alamat tujuan disediakan.');
    }

    public function testWhatsapp(Request $request)
    {
        ActivityLogger::log('test_whatsapp', 'settings:whatsapp');

        return back()->with('success', 'Konfigurasi WhatsApp Gateway tersimpan. Pengujian pesan dapat diaktifkan setelah nomor tujuan disediakan.');
    }
}
