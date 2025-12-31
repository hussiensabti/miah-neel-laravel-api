<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionNotification extends Model
{
    protected $fillable = ['user_id', 'message'];

    // 🔥 علاقة مع جدول المستخدمين
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
