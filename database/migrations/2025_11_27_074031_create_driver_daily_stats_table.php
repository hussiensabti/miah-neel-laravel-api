<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('driver_daily_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id');
            $table->date('date');
            
            $table->unsignedInteger('active_orders')->default(0);       // الطلبات الجارية
            $table->unsignedInteger('completed_orders')->default(0);   // الطلبات المكتملة
            $table->unsignedInteger('sub_orders')->default(0);         // عدد قناني الاشتراكات
            $table->unsignedInteger('net_income')->default(0);         // مجموع السعر (income)

            $table->timestamps();

            $table->foreign('driver_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_daily_stats');
    }
};
