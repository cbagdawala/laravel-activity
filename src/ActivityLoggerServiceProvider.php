<?php
// src/ActivityLoggerServiceProvider.php

namespace Cbagdawala\LaravelActivity;

use Illuminate\Support\ServiceProvider;
use Cbagdawala\LaravelActivity\Services\ActivityService;

class ActivityLoggerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    public function register(): void
    {
        $this->app->singleton(ActivityService::class, function () {
            return new ActivityService();
        });

        // Optional alias
        $this->app->alias(ActivityService::class, 'activity.service');
    }
}

