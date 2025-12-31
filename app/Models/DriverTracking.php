<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverTracking extends Model
{
    protected $table = 'driver_tracking';

    protected $fillable = [
        'user_id',
        'last_latitude',
        'last_longitude',
        'last_location_at',
        'is_tracking',
    ];

    protected $casts = [
        'last_latitude' => 'float',
        'last_longitude' => 'float',
        'last_location_at' => 'datetime',
        'is_tracking' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
