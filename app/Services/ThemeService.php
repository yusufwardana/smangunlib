<?php

namespace App\Services;

use App\Models\ThemeSetting;
use Illuminate\Support\Facades\Cache;

/**
 * ThemeService
 *
 * Satu-satunya sumber kebenaran (single source of truth) untuk konfigurasi tema.
 * Seluruh nilai dibaca dari cache; database hanya di-query satu kali lalu disimpan
 * dalam cache selamanya (rememberForever) sampai ada perubahan yang meng-invalidasi.
 *
 * Struktur penyimpanan: theme_settings(group, key, value, type)
 * Akses via helper: theme('primary_color'), theme('logo_sekolah'), dll.
 */
class ThemeService
{
    /**
     * Kunci cache utama yang menampung seluruh setting tema dalam bentuk flat map
     * "group.key" => casted value.
     */
    public const CACHE_KEY = 'theme.settings';

    /**
     * Peta default seluruh tema. Digunakan sebagai fallback bila key belum ada di DB,
     * sekaligus dasar untuk seeder & fungsi reset.
     *
     * Format: 'group' => ['key' => ['value' => mixed, 'type' => string]]
     */
    public static function defaults(): array
    {
        return [
            // 1. General Theme
            'general' => [
                'theme_name'    => ['value' => 'Default SMANGUNLIB', 'type' => 'string'],
                'mode'          => ['value' => 'light', 'type' => 'string'],   // light | dark | auto
                'border_radius' => ['value' => '1rem', 'type' => 'string'],
                'shadow_style'  => ['value' => '0 4px 20px rgba(0,0,0,0.03)', 'type' => 'string'],
                'animation'     => ['value' => true, 'type' => 'boolean'],
                'font_family'   => ['value' => "'Inter', sans-serif", 'type' => 'string'],
                'font_size'     => ['value' => '16px', 'type' => 'string'],
            ],

            // 2. Color Theme
            'color' => [
                'primary_color'    => ['value' => '#4361ee', 'type' => 'color'],
                'secondary_color'  => ['value' => '#3f37c9', 'type' => 'color'],
                'success_color'    => ['value' => '#2a9d8f', 'type' => 'color'],
                'danger_color'     => ['value' => '#e63946', 'type' => 'color'],
                'warning_color'    => ['value' => '#f77f00', 'type' => 'color'],
                'info_color'       => ['value' => '#4895ef', 'type' => 'color'],
                'background_color' => ['value' => '#f8f9fa', 'type' => 'color'],
                'sidebar_color'    => ['value' => '#ffffff', 'type' => 'color'],
                'navbar_color'     => ['value' => '#ffffff', 'type' => 'color'],
                'card_color'       => ['value' => '#ffffff', 'type' => 'color'],
                'footer_color'     => ['value' => '#ffffff', 'type' => 'color'],
                'text_color'       => ['value' => '#2b2d42', 'type' => 'color'],
                'link_color'       => ['value' => '#4361ee', 'type' => 'color'],
                'hover_color'      => ['value' => '#3a0ca3', 'type' => 'color'],
                'button_color'     => ['value' => '#4361ee', 'type' => 'color'],
            ],

            // 3. Logo
            'logo' => [
                'logo_sekolah'      => ['value' => null, 'type' => 'image'],
                'logo_perpustakaan' => ['value' => null, 'type' => 'image'],
                'logo_login'        => ['value' => null, 'type' => 'image'],
                'logo_sidebar'      => ['value' => null, 'type' => 'image'],
                'logo_footer'       => ['value' => null, 'type' => 'image'],
            ],

            // 4. Favicon
            'favicon' => [
                'favicon' => ['value' => null, 'type' => 'image'],
            ],

            // 5. Login Theme
            'login' => [
                'background_color'   => ['value' => '#4361ee', 'type' => 'color'],
                'overlay_color'      => ['value' => 'rgba(58,12,163,0.55)', 'type' => 'string'],
                'card_color'         => ['value' => '#ffffff', 'type' => 'color'],
                'button_color'       => ['value' => '#4361ee', 'type' => 'color'],
                'background_image'   => ['value' => null, 'type' => 'image'],
                'video_background'   => ['value' => null, 'type' => 'image'],
            ],

            // 6. Dashboard Theme
            'dashboard' => [
                'sidebar_color' => ['value' => '#ffffff', 'type' => 'color'],
                'navbar_color'  => ['value' => '#ffffff', 'type' => 'color'],
                'widget_color'  => ['value' => '#4361ee', 'type' => 'color'],
                'chart_color'   => ['value' => '#4361ee', 'type' => 'color'],
                'table_style'   => ['value' => 'striped', 'type' => 'string'], // striped | bordered | hover | flat
                'card_style'    => ['value' => 'shadow', 'type' => 'string'],  // flat | shadow | glass | bordered
            ],

            // 7. Landing Page Theme
            'landing' => [
                'hero_background'    => ['value' => '#4361ee', 'type' => 'color'],
                'gradient'           => ['value' => 'linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%)', 'type' => 'string'],
                'button_style'       => ['value' => 'rounded', 'type' => 'string'],
                'section_background' => ['value' => '#ffffff', 'type' => 'color'],
                'footer_background'  => ['value' => '#0d1b2a', 'type' => 'color'],
                'typography'         => ['value' => "'Inter', sans-serif", 'type' => 'string'],
            ],

            // 8. Typography
            'typography' => [
                'google_fonts' => ['value' => 'Inter', 'type' => 'string'],
                'system_fonts' => ['value' => 'sans-serif', 'type' => 'string'],
                'heading_font' => ['value' => "'Inter', sans-serif", 'type' => 'string'],
                'body_font'    => ['value' => "'Inter', sans-serif", 'type' => 'string'],
                'font_weight'  => ['value' => '400', 'type' => 'string'],
            ],

            // 9. Button Style
            'button' => [
                'shape'   => ['value' => 'rounded', 'type' => 'string'], // rounded | square | pill
                'variant' => ['value' => 'filled', 'type' => 'string'],  // filled | outline
                'shadow'  => ['value' => true, 'type' => 'boolean'],
            ],

            // 10. Card Style
            'card' => [
                'style'  => ['value' => 'shadow', 'type' => 'string'], // flat | shadow | glass | bordered
                'radius' => ['value' => '1rem', 'type' => 'string'],
            ],

            // 11. Sidebar
            'sidebar' => [
                'collapsed_default' => ['value' => false, 'type' => 'boolean'],
                'mini'              => ['value' => false, 'type' => 'boolean'],
                'width'             => ['value' => '260px', 'type' => 'string'],
                'position'          => ['value' => 'left', 'type' => 'string'], // left | right
            ],

            // 12. Navbar
            'navbar' => [
                'sticky'      => ['value' => true, 'type' => 'boolean'],
                'transparent' => ['value' => false, 'type' => 'boolean'],
                'style'       => ['value' => 'solid', 'type' => 'string'], // solid | transparent | blur
            ],

            // 13. Layout
            'layout' => [
                'mode'            => ['value' => 'full', 'type' => 'string'], // boxed | full
                'container_width' => ['value' => '1320px', 'type' => 'string'],
            ],

            // 14. Loading Screen
            'loading' => [
                'enable'        => ['value' => false, 'type' => 'boolean'],
                'spinner_style' => ['value' => 'border', 'type' => 'string'], // border | grow | dots
                'logo'          => ['value' => null, 'type' => 'image'],
            ],

            // 15 & 16. Custom CSS / JS
            'custom' => [
                'css' => ['value' => '', 'type' => 'css'],
                'js'  => ['value' => '', 'type' => 'js'],
            ],
        ];
    }

    /**
     * Ambil seluruh setting tema (flat map "group.key" => value) dari cache.
     * Query database hanya terjadi sekali; sisanya dilayani dari cache.
     */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $stored = ThemeSetting::query()
                ->get()
                ->mapWithKeys(fn (ThemeSetting $s) => [
                    $s->group.'.'.$s->key => ThemeSetting::castValue($s->value, $s->type),
                ])
                ->toArray();

            // Gabungkan dengan default agar key baru tetap tersedia walau belum ada di DB.
            $flatDefaults = [];
            foreach (self::defaults() as $group => $keys) {
                foreach ($keys as $key => $meta) {
                    $flatDefaults[$group.'.'.$key] = $meta['value'];
                }
            }

            return array_merge($flatDefaults, $stored);
        });
    }

    /**
     * Ambil satu nilai tema.
     *
     * Mendukung dua bentuk kunci:
     *  - "group.key"  => pencarian tepat, contoh theme('color.primary_color')
     *  - "key"        => pencarian singkat, contoh theme('primary_color')
     *    (dicari pada seluruh grup, hasil pertama yang cocok)
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->all();

        if (array_key_exists($key, $all)) {
            return $all[$key] ?? $default;
        }

        // Bentuk singkat: cari "*.key"
        foreach ($all as $flatKey => $value) {
            if (str_ends_with($flatKey, '.'.$key)) {
                return $value ?? $default;
            }
        }

        return $default;
    }

    /**
     * Simpan satu nilai tema lalu invalidasi cache.
     */
    public function set(string $group, string $key, mixed $value, string $type = 'string'): ThemeSetting
    {
        if (is_array($value)) {
            $value = json_encode($value);
            $type = 'json';
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        }

        $setting = ThemeSetting::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value, 'type' => $type],
        );

        $this->flush();

        return $setting;
    }

    /**
     * Simpan banyak nilai sekaligus untuk satu grup (efisien saat submit form).
     *
     * @param  array<string,mixed>  $values  [key => value]
     */
    public function setGroup(string $group, array $values): void
    {
        $defaults = self::defaults()[$group] ?? [];

        foreach ($values as $key => $value) {
            $type = $defaults[$key]['type'] ?? 'string';
            $this->set($group, $key, $value, $type);
        }
    }

    /**
     * Hapus cache tema. Dipanggil otomatis setiap set().
     */
    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Bangun string CSS variables (:root { ... }) dari nilai tema saat ini.
     * Digunakan di <head> layout agar seluruh Blade cukup memakai var(--primary-color).
     */
    public function cssVariables(): string
    {
        $map = [
            '--primary-color'    => $this->get('color.primary_color'),
            '--secondary-color'  => $this->get('color.secondary_color'),
            '--success-color'    => $this->get('color.success_color'),
            '--danger-color'     => $this->get('color.danger_color'),
            '--warning-color'    => $this->get('color.warning_color'),
            '--info-color'       => $this->get('color.info_color'),
            '--bg-color'         => $this->get('color.background_color'),
            '--sidebar-color'    => $this->get('color.sidebar_color'),
            '--navbar-color'     => $this->get('color.navbar_color'),
            '--card-color'       => $this->get('color.card_color'),
            '--footer-color'     => $this->get('color.footer_color'),
            '--text-color'       => $this->get('color.text_color'),
            '--link-color'       => $this->get('color.link_color'),
            '--hover-color'      => $this->get('color.hover_color'),
            '--button-color'     => $this->get('color.button_color'),
            // Bootstrap 5 CSS variable overrides
            '--bs-primary'       => $this->get('color.primary_color'),
            '--bs-secondary'     => $this->get('color.secondary_color'),
            '--bs-success'       => $this->get('color.success_color'),
            '--bs-danger'        => $this->get('color.danger_color'),
            '--bs-warning'       => $this->get('color.warning_color'),
            '--bs-info'          => $this->get('color.info_color'),
            '--bs-body-bg'       => $this->get('color.background_color'),
            '--bs-body-color'    => $this->get('color.text_color'),
            '--bs-body-font-family' => $this->get('general.font_family'),
            // General / layout
            '--font-family'      => $this->get('general.font_family'),
            '--font-size'        => $this->get('general.font_size'),
            '--border-radius'    => $this->get('general.border_radius'),
            '--shadow-style'     => $this->get('general.shadow_style'),
            '--sidebar-width'    => $this->get('sidebar.width'),
            '--container-width'  => $this->get('layout.container_width'),
            '--heading-font'     => $this->get('typography.heading_font'),
            '--body-font'        => $this->get('typography.body_font'),
        ];

        $lines = [];
        foreach ($map as $var => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $lines[] = sprintf('    %s: %s;', $var, $value);
        }

        return ":root {\n".implode("\n", $lines)."\n}";
    }

    /**
     * Ekspor seluruh setting tema menjadi array (siap di-JSON-kan).
     *
     * @return array<string,mixed>
     */
    public function export(): array
    {
        $data = ThemeSetting::query()
            ->get()
            ->map(fn (ThemeSetting $s) => [
                'group' => $s->group,
                'key'   => $s->key,
                'value' => $s->value,
                'type'  => $s->type,
            ])
            ->values()
            ->toArray();

        return [
            'name'        => $this->get('general.theme_name', 'Exported Theme'),
            'exported_at' => now()->toIso8601String(),
            'settings'    => $data,
        ];
    }

    /**
     * Impor setting tema dari array hasil export().
     *
     * @param  array<string,mixed>  $payload
     */
    public function import(array $payload): void
    {
        $settings = $payload['settings'] ?? [];

        foreach ($settings as $item) {
            if (! isset($item['group'], $item['key'])) {
                continue;
            }

            ThemeSetting::updateOrCreate(
                ['group' => $item['group'], 'key' => $item['key']],
                ['value' => $item['value'] ?? null, 'type' => $item['type'] ?? 'string'],
            );
        }

        $this->flush();
    }

    /**
     * Kembalikan seluruh tema ke nilai default (menghapus seluruh baris lalu re-seed default).
     */
    public function reset(): void
    {
        ThemeSetting::query()->delete();

        $rows = [];
        foreach (self::defaults() as $group => $keys) {
            foreach ($keys as $key => $meta) {
                $value = $meta['value'];
                if (is_bool($value)) {
                    $value = $value ? '1' : '0';
                } elseif (is_array($value)) {
                    $value = json_encode($value);
                }

                $rows[] = [
                    'group'      => $group,
                    'key'        => $key,
                    'value'      => $value,
                    'type'       => $meta['type'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        ThemeSetting::query()->insert($rows);
        $this->flush();
    }
}
