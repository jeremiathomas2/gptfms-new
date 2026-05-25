<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    protected static array $runtimeCache = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, self::$runtimeCache)) {
            return self::$runtimeCache[$key];
        }

        try {
            if (!Schema::hasTable('system_settings')) {
                self::$runtimeCache[$key] = $default;
                return $default;
            }
        } catch (\Throwable) {
            self::$runtimeCache[$key] = $default;
            return $default;
        }

        $value = self::query()->where('key', $key)->value('value');
        if ($value === null) {
            self::$runtimeCache[$key] = $default;
            return $default;
        }

        self::$runtimeCache[$key] = $value;
        return $value;
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        $value = self::get($key, $default ? '1' : '0');
        if (is_bool($value)) {
            return $value;
        }

        $normalized = strtolower(trim((string) $value));
        return in_array($normalized, ['1', 'true', 'yes', 'on'], true);
    }

    public static function set(string $key, mixed $value): void
    {
        $stored = is_bool($value) ? ($value ? '1' : '0') : (string) $value;

        self::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $stored]
        );

        self::$runtimeCache[$key] = $stored;
    }

    public static function setDefault(string $key, mixed $value): void
    {
        $exists = self::query()->where('key', $key)->exists();
        if (!$exists) {
            self::set($key, $value);
        } else {
            self::get($key);
        }
    }
}
