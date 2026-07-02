<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Karyawan\DailyPlanController;
use App\Http\Controllers\Karyawan\ActivityTemplateController;
use App\Http\Controllers\Manager\ApprovalController;
use App\Http\Controllers\Manager\MonitoringController;
use App\Http\Controllers\Manager\ReportController;
use App\Http\Controllers\Manager\DivisionTargetController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DivisionController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\AnnouncementViewController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Guest
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
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

    // Template aktivitas (JSON API)
    Route::prefix('templates')->name('templates.')->middleware('role:karyawan,manager,admin')->group(function () {
        Route::get('/', [ActivityTemplateController::class, 'index'])->name('index');
        Route::post('/', [ActivityTemplateController::class, 'store'])->name('store');
        Route::delete('/{template}', [ActivityTemplateController::class, 'destroy'])->name('destroy');
    });

    // Laporan & Leaderboard
    Route::middleware('role:manager,admin')->group(function () {
        Route::get('/kinerja', [ReportController::class, 'kinerja'])->name('kinerja.index');
        Route::get('/leaderboard', [ReportController::class, 'leaderboard'])->name('leaderboard.index');
        Route::get('/rekap-mingguan', [ReportController::class, 'weekly'])->name('report.weekly');
        Route::get('/targets', [DivisionTargetController::class, 'index'])->name('targets.index');
        Route::post('/targets', [DivisionTargetController::class, 'store'])->name('targets.store');
    });

    // Manager / Admin: monitoring tim
    Route::prefix('monitoring')->name('monitoring.')->middleware('role:manager,admin')->group(function () {
        Route::get('/tim', [MonitoringController::class, 'tim'])->name('tim');
        Route::get('/tim/{user}/{date}', [MonitoringController::class, 'detail'])->name('detail');
        Route::post('/feedback/{dailyPlan}', [MonitoringController::class, 'saveFeedback'])->name('feedback');
    });

    // Feedback reply (semua role bisa balas)
    Route::post('/feedback/{feedback}/reply', [MonitoringController::class, 'replyFeedback'])->name('feedback.reply');

    // Manager / Admin: persetujuan akun
    Route::prefix('approval')->name('approval.')->middleware('role:manager,admin')->group(function () {
        Route::get('/', [ApprovalController::class, 'index'])->name('index');
        Route::patch('/{user}/approve', [ApprovalController::class, 'approve'])->name('approve');
        Route::patch('/{user}/reject', [ApprovalController::class, 'reject'])->name('reject');
    });

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    // Profil
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');

    // Pengumuman (semua authenticated user bisa lihat)
    Route::get('/pengumuman', [AnnouncementViewController::class, 'index'])->name('announcements.index');
    Route::get('/pengumuman/{announcement}', [AnnouncementViewController::class, 'show'])->name('announcements.show');

    // Admin
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('divisions', DivisionController::class);
        Route::patch('users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');
        Route::get('holidays', [HolidayController::class, 'index'])->name('holidays.index');
        Route::post('holidays', [HolidayController::class, 'store'])->name('holidays.store');
        Route::post('holidays/sync', [HolidayController::class, 'sync'])->name('holidays.sync'); // full: admin.holidays.sync
        Route::delete('holidays/{holiday}', [HolidayController::class, 'destroy'])->name('holidays.destroy');
        Route::resource('announcements', AnnouncementController::class);
    });
});
