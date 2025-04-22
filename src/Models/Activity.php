<?php
// src/Models/Activity.php

namespace Cbagdawala\LaravelActivity\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends Model
{
    protected $fillable = [
        'date',
        'title',
        'description',
        'user_id',
        'activityable_id',
        'activityable_type',
    ];

    public function activityable(): MorphTo
    {
        return $this->morphTo();
    }
}

