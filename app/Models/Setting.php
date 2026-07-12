<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) return $default;

        if ($setting->type === 'boolean') return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
        if ($setting->type === 'integer') return (int) $setting->value;
        if ($setting->type === 'json') return json_decode($setting->value, true);

        return $setting->value;
    }

    public static function set($key, $value, $type = 'string')
    {
        if (is_array($value)) {
            $value = json_encode($value);
            $type = 'json';
        }

        return self::updateOrCreate(['key' => $key], [
            'value' => $value,
            'type' => $type
        ]);
    }
}
