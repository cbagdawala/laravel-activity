<?php
// src/Services/ActivityService.php

namespace Cbagdawala\LaravelActivity\Services;

use Cbagdawala\LaravelActivity\Models\Activity;

class ActivityService
{
    public function log(array $data): Activity
    {
        return Activity::create([
            'date' => $data['date'] ?? now()->toDateString(),
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'user_id' => $data['user_id'] ?? auth()->id(),
            'activityable_id' => $data['activityable_id'] ?? null,
            'activityable_type' => $data['activityable_type'] ?? null,
        ]);
    }
}

