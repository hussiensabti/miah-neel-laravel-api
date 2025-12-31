<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_notifications', function (Blueprint $table) {
            $table->id();

            // فقط علاقة مع المستخدم
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // فقط نص الإشعار
            $table->string('message');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_notifications');
    }
};
