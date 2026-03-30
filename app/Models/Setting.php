<?php

namespace App\Models;

use Throwable;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected static ?array $runtimeSettingsCache = null;

    public static function getValue($key, $default = null)
    {
        $settings = self::allValues();

        return $settings[$key] ?? $default;
    }

    public static function allValues(): array
    {
        if (self::$runtimeSettingsCache !== null) {
            return self::$runtimeSettingsCache;
        }

        try {
            self::$runtimeSettingsCache = self::query()
                ->pluck('value', 'key')
                ->all();
        } catch (Throwable) {
            // Keep pages renderable when DB is unavailable (e.g. early setup/tests).
            self::$runtimeSettingsCache = [];
        }

        return self::$runtimeSettingsCache;
    }

    public static function clearRuntimeCache(): void
    {
        self::$runtimeSettingsCache = null;
    }
}
