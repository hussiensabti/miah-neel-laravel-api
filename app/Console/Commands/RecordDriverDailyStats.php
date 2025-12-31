<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Order;
use App\Models\DriverDailyStat;
use Carbon\Carbon;

class RecordDriverDailyStats extends Command
{
    protected $signature = 'driver:record-daily-stats';
    protected $description = 'Record daily stats for all drivers';

    public function handle()
    {
        // $date = Carbon::yesterday()->toDateString();
        // $start = Carbon::yesterday()->startOfDay();
        // $end   = Carbon::yesterday()->endOfDay();

        $date = Carbon::today()->toDateString();
        $start = Carbon::today()->startOfDay();
        $end   = Carbon::today()->endOfDay();


        $drivers = User::where('role', 'driver')->get();

        foreach ($drivers as $driver) {
            $orders = Order::where('driver_id', $driver->id)
                           ->whereBetween('created_at', [$start, $end]);

            $active = (clone $orders)->where('status', 'on_delivery')->count();
            $completed = (clone $orders)->where('status', 'delivered')->count();
            $subOrders = (clone $orders)->sum('subscription_quantity');
            $totalPrice = (clone $orders)->sum('price');

            DriverDailyStat::updateOrCreate(
                [
                    'driver_id' => $driver->id,
                    'date'      => $date,
                ],
                [
                    'active_orders'   => $active,
                    'completed_orders'=> $completed,
                    'sub_orders'      => $subOrders,
                    'net_income'      => $totalPrice,
                ]
            );
        }

        $this->info("Driver daily stats recorded for date: $date");
    }
}
