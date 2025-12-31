<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\DriverProfile;
use App\Models\DriverDailyLiter;
use Carbon\Carbon;

class RecordDriverDailyLiters extends Command
{
    // 👈 هذا هو الاسم الذي تنفذه بالأمر
    protected $signature = 'driver:record-daily-liters';

    protected $description = 'Record daily liters for all drivers';

    public function handle()
    {
        $date = Carbon::today()->toDateString();

        $drivers = User::where('role', 'driver')->get();

        foreach ($drivers as $driver) {

            $profile = DriverProfile::where('user_id', $driver->id)->first();

            if (! $profile) {
                continue;
            }

            // نخزن Snapshot لليوم
            DriverDailyLiter::updateOrCreate(
                [
                    'user_id' => $driver->id,
                    'date'    => $date,
                ],
                [
                    // هنا نأخذ اللترات المتراكمة لليوم
                    'liters' => $profile->total_liters,
                ]
            );
        }

        $this->info("Driver daily liters recorded for date: $date");
    }
}
