<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_tracking', function (Blueprint $table) {
            $table->id();

            // صف واحد لكل سائق
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // آخر موقع فقط (بدقة جيدة)
            $table->decimal('last_latitude', 10, 7)->nullable();
            $table->decimal('last_longitude', 10, 7)->nullable();

            // آخر وقت تحديث للموقع (مهم للتشخيص/التوقف)
            $table->timestamp('last_location_at')->nullable();

            // هل السائق فاتح مسار حاليا
            $table->boolean('is_tracking')->default(false);

            $table->timestamps();

            // ضمان: سجل واحد فقط لكل user
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_tracking');
    }
};
