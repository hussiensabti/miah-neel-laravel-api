<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminAccountsController;

Route::middleware(['auth:sanctum'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/admins', [AdminAccountsController::class, 'index']);
        Route::post('/admins', [AdminAccountsController::class, 'store']);
        Route::put('/admins/{id}', [AdminAccountsController::class, 'update']);
        Route::delete('/admins/{id}', [AdminAccountsController::class, 'destroy']);

});

use App\Http\Controllers\Api\DriverDeliveryController;

Route::middleware(['auth:sanctum'])->prefix('driver')->group(function () {

    Route::post('delivery/track', 
        [DriverDeliveryController::class, 'track']
    );

    Route::post('delivery/mark-delivered/{order}', 
        [DriverDeliveryController::class, 'markDelivered']
    );

    Route::get('/admin/active-drivers', [DriverDeliveryController::class, 'activeDrivers']);
});

use App\Http\Controllers\Admin\DriverDailyLiterController;
Route::prefix('admin')->group(function () {
    // جميع الأيام (مع فلترة)
    Route::get(
        'drivers/{driver}/daily-liters',
        [DriverDailyLiterController::class, 'index']
    );

    // يوم محدد
    Route::get(
        'drivers/{driver}/daily-liters/{date}',
        [DriverDailyLiterController::class, 'show']
    );
});

use App\Http\Controllers\Api\Admin\AdminOrderController;
Route::get('admin/orders/dates', [AdminOrderController::class, 'dates']);
Route::get('admin/orders', [AdminOrderController::class, 'index']);


use App\Http\Controllers\Api\Admin\DriverDailyStatsController;
Route::prefix('admin')->group(function () {

    // 1️⃣ الثابت يأتي أولاً
    Route::get('driver-stats/total/{date}', [DriverDailyStatsController::class, 'totalByDate']);

    // 2️⃣ الثابت الثاني
    Route::get('driver-stats/dates', [DriverDailyStatsController::class, 'dates']);

    // 3️⃣ المتغير رقم 2
    Route::get('driver-stats/{driverId}/{date}', [DriverDailyStatsController::class, 'statsByDriverAndDate']);

    // 4️⃣ المتغير رقم 1 (الأخطر)
    Route::get('driver-stats/{date}', [DriverDailyStatsController::class, 'statsByDate']);

});

use App\Http\Controllers\Api\DriverStatsController;
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/driver/stats/today', [DriverStatsController::class, 'today']);
});

use App\Http\Controllers\AdminNotificationController;
// إرسال إشعارات (أدمن فقط)
Route::post('/admin/notifications', [AdminNotificationController::class, 'store'])
    ->middleware('auth:sanctum');

// المستخدم يحصل على إشعاراته
Route::get('/notifications', [AdminNotificationController::class, 'index'])
    ->middleware('auth:sanctum');

// عداد غير المقروء
Route::get('/notifications/unread-count', [AdminNotificationController::class, 'unreadCount'])
    ->middleware('auth:sanctum');

// تمييز الكل كمقروء
Route::post('/notifications/mark-all-read', [AdminNotificationController::class, 'markAllRead'])
    ->middleware('auth:sanctum');

// حذف (أدمن)
Route::delete('/admin/notifications/{id}', [AdminNotificationController::class, 'destroy'])
    ->middleware('auth:sanctum');

Route::get('/admin/notifications-list', [AdminNotificationController::class, 'all'])
    ->middleware('auth:sanctum');

Route::post('/notifications/driver-arrived', [AdminNotificationController::class, 'driverArrived']);
    
use App\Http\Controllers\Admin\DriverProfileController;
Route::prefix('admin/drivers')->group(function () {
    Route::get('{id}/basic', [DriverProfileController::class, 'showBasic']);
    Route::post('{id}/basic', [DriverProfileController::class, 'updateBasic']);

    Route::get('{id}/liters', [DriverProfileController::class, 'showLiters']);
    Route::put('{id}/liters', [DriverProfileController::class, 'updateLiters']);
});

use App\Http\Controllers\Admin\AdminSupervisorController;
Route::prefix('admin')->group(function () {
    Route::get('/supervisors', [AdminSupervisorController::class, 'index']);
    Route::post('/supervisors', [AdminSupervisorController::class, 'store']);
    Route::put('/supervisors/{id}', [AdminSupervisorController::class, 'updatePassword']);
    Route::delete('/supervisors/{id}', [AdminSupervisorController::class, 'destroy']);
});

use App\Http\Controllers\Api\Admin\UserController;
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users/{id}/toggle', [UserController::class, 'toggleSubscription']);
    Route::get('/users/{id}/subscription', [UserController::class, 'subscriptionDetails']);
    Route::get('/subscription-notifications', [UserController::class, 'logs']);
});

use App\Http\Controllers\Api\OrderController;
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
    Route::get('/driver/orders', [OrderController::class, 'driverOrders']);
    // Route::post('/orders/{id}/delivered', [OrderController::class, 'markDelivered']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
});

use App\Http\Controllers\DriverZoneController;
// 🗺️ مناطق السائقين
Route::prefix('admin')->group(function () {
    // جلب جميع المناطق
    Route::get('/driver-zones', [DriverZoneController::class, 'index']);

    // جلب منطقة واحدة لسائق معيّن
    Route::get('/driver-zones/{driver}', [DriverZoneController::class, 'byDriver']);

    // إنشاء أو تحديث منطقة السائق
    Route::post('/driver-zones', [DriverZoneController::class, 'storeOrUpdate']);

    // حذف منطقة معينة
    Route::delete('/driver-zones/{id}', [DriverZoneController::class, 'destroy']);
});

use App\Http\Controllers\Admin\DriverController;
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::get('/drivers', [DriverController::class, 'index']);
    Route::post('/drivers', [DriverController::class, 'store']);
    Route::put('/drivers/{id}', [DriverController::class, 'updatePassword']);
    Route::delete('/drivers/{id}', [DriverController::class, 'destroy']);
});

use App\Http\Controllers\Auth\AuthenticatedSessionController;
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::middleware('auth:sanctum')->post('/logout', [AuthenticatedSessionController::class, 'destroy']);

use App\Http\Controllers\Auth\RegisteredUserController;
Route::post('/register', [RegisteredUserController::class, 'store']);


// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });
