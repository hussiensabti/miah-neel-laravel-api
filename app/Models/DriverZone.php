<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'name',
        'coordinates',
    ];

    protected $casts = [
        'coordinates' => 'array',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function user()
    {
    return $this->belongsTo(User::class, 'driver_id');
    }
}