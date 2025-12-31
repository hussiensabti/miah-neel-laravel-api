<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // كمية داخل الاشتراك
            $table->unsignedInteger('subscription_quantity')
                  ->default(0)
                  ->after('quantity');

            // كمية فوق الاشتراك
            $table->unsignedInteger('over_quantity')
                  ->default(0)
                  ->after('subscription_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['subscription_quantity', 'over_quantity']);
        });
    }
};
