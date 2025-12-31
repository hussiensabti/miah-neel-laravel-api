<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DriverDailyLiter extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'liters',
    ];

    protected $casts = [
        'date' => 'date',
        'liters' => 'double',
    ];

    // العلاقة مع السائق
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}