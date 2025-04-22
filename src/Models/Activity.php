<?php

namespace Cbagdawala\LaravelActivity\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Config;

class Activity extends Model
{
    protected $table = 'activity_log';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('activity.table', 'activity_log');
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when(isset($filters['date_from']), fn($q) =>
            $q->whereDate('date', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn($q) =>
            $q->whereDate('date', '<=', $filters['date_to']))
            ->when(isset($filters['user_id']), fn($q) =>
            $q->where('user_id', $filters['user_id']))
            ->when(isset($filters['title']), fn($q) =>
            $q->where('title', 'like', '%' . $filters['title'] . '%'))
            ->when(isset($filters['activityable_type']), fn($q) =>
            $q->where('activityable_type', $filters['activityable_type']));
    }


    protected $fillable = [
        'date',
        'title',
        'description',
        'user_id',
        'activityable_id',
        'activityable_type',
    ];

    public function activityable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        $model = Config::get('activity.user_class', 'App\Models\User');
        if (!class_exists($model)) {
            throw new \Exception("User model class $model does not exist.");
        }
        return $this->belongsTo($model, Config::get('activity.user_id', 'user_id'));
    }
}

