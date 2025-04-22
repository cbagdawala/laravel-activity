<?php

namespace Cbagdawala\LaravelActivity\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Cbagdawala\LaravelActivity\Services\ActivityCollector;
use Illuminate\Support\Facades\Auth;

class ActivityObserver
{
    protected $now;

    public function __construct()
    {
        $this->now = now();
    }

    public function created($model): void
    {
        $this->logActivity($model, 'Created');
    }

    public function updated($model): void
    {
        $this->logActivity($model, 'Updated');
    }

    public function deleted($model): void
    {
        $this->logActivity($model, 'Deleted');
    }

    protected function logActivity($model, $action): void
    {
        if (!Config::get('activity.log_enabled')) {
            return;
        }

        $guard = Config::get('system.auth.admin', 'admin');
        //if (array_key_exists($guard, Config::get('auth.guards', []))) {
            $userId = 1;//Auth::guard($guard)->id();

            $data = [
                'title'             => "$action: " . class_basename($model),
                'description'       => "$action record with ID " . $model->id,
                'activityable_id'   => $model->id,
                'activityable_type' => get_class($model),
                'user_id'           => $userId,
                'date'              => $this->now,
            ];

            // Log activity data to session
            ActivityCollector::add($data);
        //}
    }
}

