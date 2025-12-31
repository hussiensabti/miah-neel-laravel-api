<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverDailyStat extends Model
{
    protected $table = 'driver_daily_stats';

    protected $fillable = [
        'driver_id',
        'date',
        'active_orders',
        'completed_orders',
        'sub_orders',
        'net_income',
    ];

    // إذا تحب: علاقة مع User
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
