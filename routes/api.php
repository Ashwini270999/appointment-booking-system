<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DoctorAvailabilityController;
use App\Http\Controllers\Api\AppointmentController;


Route::prefix('v1')->group(function () {

    Route::post(
        '/availabilities',
        [DoctorAvailabilityController::class, 'store']
    );

    Route::get(
    '/doctors/{doctor}/slots',
    [DoctorAvailabilityController::class, 'availableSlots']
    );

    Route::post(
    '/appointments',
    [AppointmentController::class, 'book']
    );

    Route::post(
    '/appointments/{appointment}/cancel',
    [AppointmentController::class, 'cancel']
    );

    Route::post(
    '/appointments/{appointment}/reschedule',
    [AppointmentController::class, 'reschedule']
    );

});
