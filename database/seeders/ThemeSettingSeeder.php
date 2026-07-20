<?php

namespace Database\Seeders;

use App\Models\ThemeSetting;
use App\Services\ThemeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

/**
 * ThemeSettingSeeder
 *
 * Mengisi tabel theme_settings dengan seluruh nilai default dari ThemeService::defaults().
 * Menggunakan updateOrCreate sehingga aman dijalankan berulang (idempotent) tanpa
 * menimpa nilai yang sudah pernah diubah admin — kecuali key yang belum ada.
 */
class ThemeSettingSeeder extends Seeder
{
    public function run(): void
    {
        foreach (ThemeService::defaults() as $group => $keys) {
            foreach ($keys as $key => $meta) {
                $value = $meta['value'];

                if (is_bool($value)) {
                    $value = $value ? '1' : '0';
                } elseif (is_array($value)) {
                    $value = json_encode($value);
                }

                ThemeSetting::firstOrCreate(
                    ['group' => $group, 'key' => $key],
                    ['value' => $value, 'type' => $meta['type']],
                );
            }
        }

        // Bersihkan cache agar nilai baru langsung terbaca.
        Cache::forget(ThemeService::CACHE_KEY);
    }
}
