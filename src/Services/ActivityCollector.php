<?php

namespace Cbagdawala\LaravelActivity\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class ActivityCollector
{
    public static function add(array $data): void
    {
        $activities = static::fetchActivities() ?? [];
        $activities[] = $data;
        static::storeActivities($activities);
    }

    protected static function fetchActivities()
    {
        return Cache::get(Config::get('activity.session_key', 'activity_log_data'), []);
    }

    protected static function storeActivities(array $activities): void
    {
        Cache::put(Config::get('activity.session_key', 'activity_log_data'), $activities);
    }

    public static function addToQueue(array $data): void
    {
        $activities = static::fetchActivitiesFromQueue() ?? [];
        $activities[] = $data;
        Cache::put(Config::get('activity.queue_key', 'activity_log_queue'), $activities);
    }

    //add to queue

    public static function fetchActivitiesFromQueue()
    {
        return Cache::get(Config::get('activity.queue_key', 'activity_log_queue'), []);
    }

    public static function all(): array
    {
        return static::fetchActivities() ?? [];
    }

    public static function clear(): void
    {
        Cache::forget(Config::get('activity.session_key', 'activity_log_data'));
        Cache::forget(Config::get('activity.queue_key', 'activity_log_queue'));
    }
}

