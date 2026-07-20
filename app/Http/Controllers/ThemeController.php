<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportThemeRequest;
use App\Http\Requests\UpdateThemeRequest;
use App\Models\ThemeSetting;
use App\Services\ActivityLogger;
use App\Services\ThemeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


/**
 * ThemeController
 *
 * Mengelola modul Theme Manager: menampilkan halaman, menyimpan perubahan
 * (mendukung AJAX untuk live preview), reset, export, dan import tema.
 */
class ThemeController extends Controller
{
    /**
     * Peran yang berhak mengelola tema. Rute sudah dilindungi middleware
     * role:super_admin|kepala_perpustakaan; pemeriksaan ini adalah lapis
     * pertahanan kedua agar controller tetap aman meski dipanggil langsung.
     */
    private const AUTHORIZED_ROLES = ['super_admin', 'kepala_perpustakaan'];

    public function __construct(private readonly ThemeService $theme)
    {
    }

    /**
     * Pastikan pengguna berhak mengelola tema; jika tidak, hentikan dengan 403.
     */
    private function authorizeTheme(): void
    {
        abort_unless(
            (bool) Auth::user()?->hasAnyRole(self::AUTHORIZED_ROLES),
            403,
            'Anda tidak memiliki izin untuk mengelola tema.'
        );
    }


    /**
     * Tampilkan halaman Theme Manager.
     */
    public function index()
    {
        $this->authorizeTheme();

        $defaults = ThemeService::defaults();
        $values = $this->theme->all();

        return view('system.theme.index', compact('defaults', 'values'));
    }

    /**
     * Simpan pengaturan tema untuk satu grup.
     * Mendukung request AJAX (mengembalikan JSON + CSS variables untuk live preview).
     */
    public function update(UpdateThemeRequest $request)
    {
        $this->authorizeTheme();

        $group = $request->string('group')->toString();
        $before = $this->theme->all();

        // 1. Simpan nilai teks/warna/select/boolean.
        $settings = $request->input('settings', []) ?? [];

        // Normalisasi boolean: field boolean pada defaults yang tidak dikirim => false.
        $groupDefaults = ThemeService::defaults()[$group] ?? [];
        foreach ($groupDefaults as $key => $meta) {
            if (($meta['type'] ?? null) === 'boolean' && ! array_key_exists($key, $settings)) {
                $settings[$key] = false;
            }
        }

        foreach ($settings as $key => $value) {
            $type = $groupDefaults[$key]['type'] ?? 'string';

            if ($type === 'boolean') {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }

            $this->theme->set($group, $key, $value, $type);
        }

        // 2. Simpan file upload (logo, favicon, background, dll).
        foreach ($request->file('uploads', []) ?? [] as $key => $file) {
            if (! $file) {
                continue;
            }

            // Hapus aset lama bila ada.
            $old = $this->theme->get($group.'.'.$key);
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }

            $path = $file->store('theme/'.$group, 'public');
            $this->theme->set($group, $key, $path, 'image');
        }

        $after = $this->theme->all();
        ActivityLogger::log('update_theme', 'theme:'.$group, 0, $before, $after);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'       => true,
                'message'       => 'Tema berhasil disimpan.',
                'css_variables' => $this->theme->cssVariables(),
                'values'        => $this->theme->all(),
            ]);
        }

        return back()->with('success', 'Tema berhasil disimpan.');
    }

    /**
     * Preview real-time tanpa menyimpan ke database.
     * Menerima payload warna/nilai dan mengembalikan blok CSS variables.
     */
    public function preview(Request $request): JsonResponse
    {
        $this->authorizeTheme();

        $overrides = $request->input('settings', []) ?? [];

        // Bangun map variabel dari nilai tersimpan lalu timpa dengan override preview.
        $current = $this->theme->all();
        $merged = $current;
        $group = $request->input('group');

        foreach ($overrides as $key => $value) {
            if ($group) {
                $merged[$group.'.'.$key] = $value;
            } else {
                $merged[$key] = $value;
            }
        }

        // Petakan ke CSS variables (subset penting untuk preview cepat).
        $cssMap = [
            '--primary-color'   => $merged['color.primary_color'] ?? null,
            '--secondary-color' => $merged['color.secondary_color'] ?? null,
            '--success-color'   => $merged['color.success_color'] ?? null,
            '--danger-color'    => $merged['color.danger_color'] ?? null,
            '--warning-color'   => $merged['color.warning_color'] ?? null,
            '--info-color'      => $merged['color.info_color'] ?? null,
            '--bg-color'        => $merged['color.background_color'] ?? null,
            '--sidebar-color'   => $merged['color.sidebar_color'] ?? null,
            '--navbar-color'    => $merged['color.navbar_color'] ?? null,
            '--card-color'      => $merged['color.card_color'] ?? null,
            '--footer-color'    => $merged['color.footer_color'] ?? null,
            '--text-color'      => $merged['color.text_color'] ?? null,
            '--link-color'      => $merged['color.link_color'] ?? null,
            '--hover-color'     => $merged['color.hover_color'] ?? null,
            '--button-color'    => $merged['color.button_color'] ?? null,
            '--font-family'     => $merged['general.font_family'] ?? null,
            '--font-size'       => $merged['general.font_size'] ?? null,
            '--border-radius'   => $merged['general.border_radius'] ?? null,
            '--sidebar-width'   => $merged['sidebar.width'] ?? null,
        ];

        $lines = [];
        foreach ($cssMap as $var => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $lines[] = sprintf('%s: %s;', $var, $value);
        }

        return response()->json([
            'success'       => true,
            'css_variables' => ':root {'.implode(' ', $lines).'}',
        ]);
    }

    /**
     * Reset seluruh tema ke default.
     */
    public function reset(Request $request)
    {
        $this->authorizeTheme();

        $before = $this->theme->all();
        $this->theme->reset();
        $after = $this->theme->all();

        ActivityLogger::log('reset_theme', 'theme:all', 0, $before, $after);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'       => true,
                'message'       => 'Tema dikembalikan ke default.',
                'css_variables' => $this->theme->cssVariables(),
            ]);
        }

        return back()->with('success', 'Tema dikembalikan ke default.');
    }

    /**
     * Export tema sebagai file JSON.
     */
    public function export()
    {
        $this->authorizeTheme();

        $payload = $this->theme->export();
        $filename = 'theme-'.now()->format('Ymd-His').'.json';

        ActivityLogger::log('export_theme', 'theme:all');

        return response()->json($payload, 200, [
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Import tema dari file JSON.
     */
    public function import(ImportThemeRequest $request)
    {
        $this->authorizeTheme();

        $content = file_get_contents($request->file('file')->getRealPath());
        $payload = json_decode($content, true);

        if (! is_array($payload) || ! isset($payload['settings'])) {
            return back()->with('error', 'File tema tidak valid.');
        }

        $before = $this->theme->all();
        $this->theme->import($payload);
        $after = $this->theme->all();

        ActivityLogger::log('import_theme', 'theme:all', 0, $before, $after);

        return back()->with('success', 'Tema berhasil diimpor.');
    }
}
