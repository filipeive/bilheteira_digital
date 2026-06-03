<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group'];

    /**
     * Obter valor de uma configuração com cache.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            try {
                $setting = static::where('key', $key)->first();
                return $setting?->value ?? $default;
            } catch (QueryException) {
                return $default;
            }
        });
    }

    /**
     * Legacy alias for get().
     */
    public static function getValue(string $key, ?string $default = null): ?string
    {
        return static::get($key, $default);
    }

    /**
     * Definir valor e limpar cache.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting_{$key}");
    }

    /**
     * Legacy alias for set().
     */
    public static function putValue(string $key, ?string $value): void
    {
        static::set($key, $value);
    }

    /**
     * Obter grupo completo de configurações.
     */
    public static function group(string $group): array
    {
        return Cache::remember("settings_group_{$group}", 3600, function () use ($group) {
            try {
                return static::where('group', $group)->pluck('value', 'key')->toArray();
            } catch (QueryException) {
                return [];
            }
        });
    }

    /**
     * Get multiple values with defaults.
     */
    public static function values(array $defaults = []): array
    {
        try {
            return array_replace(
                $defaults,
                static::query()
                    ->whereIn('key', array_keys($defaults))
                    ->pluck('value', 'key')
                    ->filter(fn ($value) => $value !== null && $value !== '')
                    ->toArray()
            );
        } catch (QueryException) {
            return $defaults;
        }
    }
}
