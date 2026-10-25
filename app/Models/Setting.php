<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    protected static array $cache = [];

    protected static bool $loaded = false;

    public static function loadAll(): void
    {
        if (static::$loaded) {
            return;
        }

        try {
            $all = static::all();
            foreach ($all as $item) {
                $decoded = json_decode($item->value, true);
                static::$cache[$item->key] = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $item->value;
            }
            static::$loaded = true;
        } catch (\Throwable $e) {
            // Fail gracefully if DB temporarily unreachable
        }
    }

    public static function get(string $key, $default = null)
    {
        static::loadAll();

        if (array_key_exists($key, static::$cache)) {
            return static::$cache[$key];
        }

        return $default;
    }

    public static function set(string $key, $value): void
    {
        $val = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;
        static::updateOrCreate(['key' => $key], ['value' => $val]);

        $decoded = is_array($value) || is_object($value) ? $value : json_decode($val, true);
        static::$cache[$key] = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $val;
    }
}
