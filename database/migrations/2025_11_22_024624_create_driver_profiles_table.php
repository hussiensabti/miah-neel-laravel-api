<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_profiles', function (Blueprint $table) {
            $table->id();

            // الربط مع جدول users
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // نوع السيارة
            $table->string('car_type')->nullable();

            // صور الوثائق (List من الروابط)
            $table->json('documents')->nullable();

            // مجموع اللترات
            $table->double('total_liters')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_profiles');
    }
};
