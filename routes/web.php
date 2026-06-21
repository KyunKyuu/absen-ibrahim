<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::view('/demo-dashboard', 'demo.dashboard')->name('demo.dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::post('/admin/users/link-parent', [AdminController::class, 'linkParent'])->name('admin.users.link-parent');
        Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
        Route::post('/admin/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
        Route::post('/admin/classes', [AdminController::class, 'storeClass'])->name('admin.classes.store');
        Route::post('/admin/iot-devices', [AdminController::class, 'storeDevice'])->name('admin.iot-devices.store');
    });

    Route::middleware('role:student')->group(function () {
        Route::post('/attendance/check-in', [AttendanceController::class, 'store'])->name('attendance.check-in');
    });

    Route::middleware('role:admin,teacher')->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/teacher/assessments', [AssessmentController::class, 'create'])->name('teacher.assessments');
        Route::post('/teacher/assessments/attitude', [AssessmentController::class, 'storeAttitude'])->name('teacher.assessments.attitude');
        Route::post('/teacher/assessments/achievement', [AssessmentController::class, 'storeAchievement'])->name('teacher.assessments.achievement');
        Route::get('/reports/attendance.csv', [ReportController::class, 'attendanceCsv'])->name('reports.attendance');
        Route::get('/reports/attitude.csv', [ReportController::class, 'attitudeCsv'])->name('reports.attitude');
    });
});
