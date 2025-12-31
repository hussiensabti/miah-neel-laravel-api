<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'quantity',
    'subscription_quantity',
    'over_quantity',
    'price',
    'notes',
    'latitude',
    'longitude',
    'status',
    'is_over_subscription',
    'driver_id',
    ];

    protected $casts = [
        'is_over_subscription' => 'boolean',
    ];

    // علاقة الطلب بالمستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function driver()
{
    return $this->belongsTo(User::class, 'driver_id');
}

}
