<?php

namespace Cbagdawala\LaravelActivity\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class ActivityCollector
{
    protected static function fetchActivities()
    {
        return Cache::get(Config::get('activity.session_key', 'activity_log_data'), []);
    }

    protected static function storeActivities(array $activities): void
    {
        Cache::put(Config::get('activity.session_key', 'activity_log_data'), $activities);
    }

    public static function add(array $data): void
    {
        $activities = static::fetchActivities() ?? [];
        $activities[] = $data;
        static::storeActivities($activities);
    }

    public static function all(): array
    {
        return static::fetchActivities() ?? [];
    }

    public static function clear(): void
    {
        Cache::forget(Config::get('activity.session_key', 'activity_log_data'));
    }
}

