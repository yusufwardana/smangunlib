<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ThemeSetting
 *
 * Menyimpan seluruh konfigurasi tema aplikasi (warna, logo, tipografi, layout, dll)
 * dalam bentuk key-value yang dikelompokkan oleh kolom "group".
 *
 * Gunakan {@see \App\Services\ThemeService} atau helper theme() untuk membaca nilai
 * agar seluruh akses tercache dan tidak melakukan query berulang.
 *
 * @property int    $id
 * @property string $group
 * @property string $key
 * @property string|null $value
 * @property string $type
 */
class ThemeSetting extends Model
{
    protected $table = 'theme_settings';

    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
    ];

    /**
     * Casting nilai mentah dari database menjadi tipe PHP sesuai kolom "type".
     */
    public function castedValue(): mixed
    {
        return static::castValue($this->value, $this->type);
    }

    /**
     * Helper statis untuk melakukan casting sebuah nilai berdasarkan tipenya.
     */
    public static function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'json'    => is_array($value) ? $value : (json_decode((string) $value, true) ?? []),
            default   => $value,
        };
    }
}
