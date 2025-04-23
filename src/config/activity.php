<?php

return [

    //Logging enabled
    'log_enabled' => env('ACTIVITY_LOG_ENABLED', true),

    //Default table name
    'table' => 'activity_log',

    //Session key
    'session_key' => 'activity_log_data',

    //Queue key
    'queue_key' => 'activity_log_queue',

    //Models where ActivityObserver is applied
    'models' => [],

    //User Class
    'user_class' => 'App\Models\User',

    //User Relation ID
    'user_id' => 'user_id',
];
