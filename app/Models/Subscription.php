<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_quantity',
        'used_quantity',
        'starts_at',
        'ends_at',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    // ============================
    // العلاقة مع المستخدم
    // ============================
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ============================
    // سكوب يجلب الاشتراك النشط
    // ============================
    public function scopeActiveForUser($query, int $userId)
    {
        return $query->where('user_id', $userId)
            ->where('status', 'active')
            ->where('ends_at', '>=', now());
    }

    // ============================
    // هل الاشتراك منتهي زمنياً؟
    // ============================
    public function isExpiredByTime()
    {
        return now()->greaterThan($this->ends_at);
    }

    // ============================
    // هل الاشتراك منتهي بالكمية؟
    // ============================
    public function isExpiredByQuantity()
    {
        return $this->used_quantity >= $this->total_quantity;
    }

    // ============================
    // تحديث حالة الاشتراك
    // ============================
    public function refreshStatus()
    {
        if ($this->isExpiredByTime() || $this->isExpiredByQuantity()) {
            $this->update(['status' => 'expired']);
        }
    }
}