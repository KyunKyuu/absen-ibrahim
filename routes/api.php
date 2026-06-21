<?php

use App\Http\Controllers\Api\IotAttendanceController;
use Illuminate\Support\Facades\Route;

Route::post('/iot/attendance', [IotAttendanceController::class, 'store'])
    ->middleware('throttle:60,1')
    ->name('api.iot.attendance');
