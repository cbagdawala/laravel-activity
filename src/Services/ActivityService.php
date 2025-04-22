<?php
// src/Services/ActivityService.php

namespace Cbagdawala\LaravelActivity\Services;

use Cbagdawala\LaravelActivity\Models\Activity;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ActivityService
{
    protected string $now;

    public function __construct()
    {
        $this->now = now()->format('Y-m-d H:i:s');
    }

    public function log(array $data): Activity
    {
        $title = $data['title'] ?? null;
        $model = $data['activityable_type'] ?? null;
        if ($model) {
            $m = $model::query()
                ->withoutGlobalScopes()
                ->find($data['activityable_id']);
            if ($m) {
                $activityLogColumn = self::getActivityLogColumn($model);
                $title .= ': ' . $m->$activityLogColumn;
            }
        }

        return Activity::query()
            ->create([
                'date'              => $data['date'] ?? $this->now,
                'title'             => $title,
                'description'       => $data['description'] ?? null,
                'user_id'           => $data['user_id'] ?? null,
                'activityable_id'   => $data['activityable_id'] ?? null,
                'activityable_type' => $data['activityable_type'] ?? null,
            ]);
    }

    public static function fetch(array $filters = [], int $perPage = 15)
    {
        return Activity::query()
            ->with('activityable')
            ->filter($filters)
            ->latest()
            ->paginate($perPage);
    }

    private static function getActivityLogColumn($model)
    {
        $models = Config::get('activity.models', []);

        //find the model in the config file and get the activity_log_column
        //$class = is_object($model) ? get_class($model) : $model;
        $matched = collect($models)->first(fn($item) => $item['class'] === $model);

        $column = 'id';
        if ($matched) {
            $column = $matched['activityLogColumn'];
        }

        return $column;
    }
}

