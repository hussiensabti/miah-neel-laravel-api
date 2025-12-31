<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // صاحب الطلب (الزبون فقط)
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // عدد القناني المطلوبة
            $table->unsignedInteger('quantity')->default(1);

            // السعر النهائي للطلب
            $table->unsignedInteger('price');

            // ملاحظات الطلب
            $table->text('notes')->nullable();

            // إحداثيات موقع الطلب
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            // حالة الطلب (قيد التوصيل أو مكتمل)
            $table->enum('status', ['on_delivery', 'delivered'])
                  ->default('on_delivery');

            // هل يتجاوز الطلب الحد المتبقي من الاشتراك؟
            $table->boolean('is_over_subscription')
                  ->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
