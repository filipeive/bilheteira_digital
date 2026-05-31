<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        try {
            return static::query()->where('key', $key)->value('value') ?? $default;
        } catch (QueryException) {
            return $default;
        }
    }

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

    public static function putValue(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
