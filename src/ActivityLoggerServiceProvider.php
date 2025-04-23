<?php
// src/ActivityLoggerServiceProvider.php

namespace Cbagdawala\LaravelActivity;

use Cbagdawala\LaravelActivity\Services\ActivityCollector;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Cbagdawala\LaravelActivity\Services\ActivityService;
use Cbagdawala\LaravelActivity\Observers\ActivityObserver;

class ActivityLoggerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/config/activity.php' => config_path('activity.php'),
        ], 'activity');

        $this->mergeConfigFrom(
            __DIR__.'/config/activity.php', 'activity'
        );

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        // Register the transaction listener
        $this->registerTransactionListener();

        // Register the model observers
        $this->registerModelObservers();
    }

    public function register(): void
    {
        $this->app->singleton(ActivityService::class, function () {
            return new ActivityService();
        });

        // Optional alias
        $this->app->alias(ActivityService::class, 'activity.service');
    }

    protected function registerTransactionListener(): void
    {
        // Use the negative condition in the constructor
        if (!Config::get('activity.log_enabled')) {
            // If logging is disabled, don't register any listeners or services
            return;
        }

        // Hook into the transaction commit process
        DB::afterCommit(function () {
            $this->logActivityAfterCommit();
        });
    }

    protected function logActivityAfterCommit(): void
    {
        $activities = ActivityCollector::all();

        if (empty($activities)) {
            return;
        }

        $as = new ActivityService();
        foreach ($activities as $activity) {
            $as->log($activity);
        }

        ActivityCollector::clear();
    }

    /**
     * Register ActivityObserver for models defined in config.
     */
    protected function registerModelObservers(): void
    {
        $models = Config::get('activity.models', []);

        foreach ($models as $m) {
            $model = $m['class'];
            if (class_exists($model)) {
                $model::observe(ActivityObserver::class);
            }
        }
    }
}

