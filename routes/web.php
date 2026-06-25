<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Karyawan\DailyPlanController;
use App\Http\Controllers\Manager\MonitoringController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DivisionController;
use Illuminate\Support\Facades\Route;

// Guest
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated
Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Karyawan (juga bisa diakses manager/admin untuk lihat kalender diri sendiri)
    Route::prefix('kalender')->name('karyawan.')->middleware('role:karyawan,manager,admin')->group(function () {
        Route::get('/', [DailyPlanController::class, 'index'])->name('kalender');
        Route::get('/{date}', [DailyPlanController::class, 'show'])->name('daily');
        Route::post('/{date}/plan', [DailyPlanController::class, 'storePlan'])->name('plan.store');
        Route::post('/{date}/report', [DailyPlanController::class, 'storeReport'])->name('report.store');
    });

    // Manager / Admin: monitoring tim
    Route::prefix('monitoring')->name('monitoring.')->middleware('role:manager,admin')->group(function () {
        Route::get('/tim', [MonitoringController::class, 'tim'])->name('tim');
        Route::get('/tim/{user}/{date}', [MonitoringController::class, 'detail'])->name('detail');
        Route::post('/feedback/{dailyPlan}', [MonitoringController::class, 'saveFeedback'])->name('feedback');
    });

    // Admin
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('divisions', DivisionController::class);
        Route::patch('users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');
    });
});
