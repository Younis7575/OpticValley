<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function all_cached(): array
    {
        return Cache::rememberForever('settings.all', fn () => static::pluck('value', 'key')->all());
    }

    public static function get(string $key, $default = null)
    {
        $v = static::all_cached()[$key] ?? null;

        return ($v === null || $v === '') ? $default : $v;
    }

    public static function put(array $values): void
    {
        foreach ($values as $k => $v) {
            static::updateOrCreate(['key' => $k], ['value' => is_array($v) ? json_encode($v) : $v]);
        }
        Cache::forget('settings.all');
    }
}
