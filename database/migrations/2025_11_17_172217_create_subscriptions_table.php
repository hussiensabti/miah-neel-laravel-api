<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            // صاحب الاشتراك
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // عدد القناني المسموح بها (مثلاً 10)
            $table->unsignedInteger('total_quantity')->default(10);

            // عدد القناني التي تم استهلاكها
            $table->unsignedInteger('used_quantity')->default(0);

            // بدايته ونهايته
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');

            // هل الاشتراك فعّال أم منتهي؟
            $table->enum('status', ['active', 'expired'])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};