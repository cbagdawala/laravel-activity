<?php

namespace Cbagdawala\LaravelActivity\Observers;

use Cbagdawala\LaravelActivity\Services\ActivityCollector;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

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

    protected function logActivity($model, $action): void
    {
        if (!Config::get('activity.log_enabled')) {
            return;
        }

        $guard = Config::get('system.auth.admin', 'admin');
        if (array_key_exists($guard, Config::get('auth.guards', []))) {
            $userId = Auth::guard($guard)->id();

            $activityableId = $model->id;
            $activityableType = get_class($model);

            $title = "$action: " . class_basename($model);
            if ($model) {
                if ($action == 'Deleted') {
                    $queues = ActivityCollector::fetchActivitiesFromQueue();
                    $m = collect($queues)->where(fn($item) => $item['modelId'] == $activityableId)->first();
                } else {
                    $m = $activityableType::query()
                        ->withoutGlobalScopes()
                        ->find($activityableId);
                }
                if ($m) {
                    $activityLogColumn = $this->getActivityLogColumn($activityableType);
                    $title .= ': ' . $model->$activityLogColumn;
                }
            }

            $data = [
                'modelId'           => $model->id,
                'model'             => $model,
                'title'             => $title,
                'description'       => "$action record with ID " . $model->id,
                'activityable_id'   => $activityableId,
                'activityable_type' => $activityableType,
                'user_id'           => $userId,
                'date'              => $this->now,
                'action'            => $action,
            ];

            // Log activity data to session
            if ($action == 'Deleting') {
                ActivityCollector::addToQueue($data);
            } else {
                ActivityCollector::add($data);
            }
        }
    }

    //deleting

    private function getActivityLogColumn($model)
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

    public function updated($model): void
    {
        $this->logActivity($model, 'Updated');
    }

    public function deleting($model): void
    {
        $this->logActivity($model, 'Deleting');
    }

    public function deleted($model): void
    {
        $this->logActivity($model, 'Deleted');
    }

}

