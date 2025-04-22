<?php
use Cbagdawala\LaravelActivity\Services\ActivityService;

if (!function_exists('activity')) {
    function activity(): ActivityService
    {
        return app(ActivityService::class);
    }
}
