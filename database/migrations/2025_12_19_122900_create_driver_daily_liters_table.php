<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_daily_liters', function (Blueprint $table) {
            $table->id();

            // السائق
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // تاريخ اليوم (Business Date)
            $table->date('date');

            // مجموع اللترات لذلك اليوم
            $table->double('liters')->default(0);

            $table->timestamps();

            // ضمان عدم تكرار سجل لنفس السائق بنفس اليوم
            $table->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_daily_liters');
    }
};