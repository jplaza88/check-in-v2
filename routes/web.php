<?php

declare(strict_types=1);

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\CheckInDistanceController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GeoController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

$locales = implode('|', config('app.locales'));

Route::prefix('locale')->group(function () use ($locales): void {

    Route::post('/{locale}', LocaleController::class)
        ->where('locale', $locales)
        ->name('localeSwitch');
});

// JSON endpoint, no translations needed
Route::middleware('throttle:30,1')
    ->post('/check-in/distance', CheckInDistanceController::class)
    ->name('checkIn.distance');

// JSON geo-autocomplete endpoints, no translations needed
Route::middleware('throttle:60,1')->group(function (): void {

    Route::get('/geo/cities', [GeoController::class, 'cities'])->name('geo.cities');

    Route::get('/geo/states', [GeoController::class, 'states'])->name('geo.states');
});

Route::middleware('setLocale')->group(function (): void {

    /**
     * Public
     */
    Route::inertia('/', 'Home')->name('home');

    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule');

    Route::get('/contact', [ContactController::class, 'index'])->name('contact');

    /**
     * Check-In
     */
    Route::get('/check-in/select-location', [CheckInController::class, 'selectLocation'])
        ->name('checkIn.selectLocation');

    Route::middleware(['userCoordinates', 'throttle:30,1'])
        ->post('/check-in/{uuid}', [CheckInController::class, 'gate'])
        ->whereUuid('uuid')
        ->name('checkIn.gate');

    Route::middleware('checkInGatePass')->group(function (): void {

        Route::get('/check-in/{uuid}/form', [CheckInController::class, 'form'])
            ->whereUuid('uuid')
            ->name('checkIn.form');

        Route::middleware('throttle:10,1')
            ->post('/check-in/{uuid}/form', [CheckInController::class, 'store'])
            ->whereUuid('uuid')
            ->name('checkIn.store');
    });

    /**
     * Appointment
     */
    Route::get('/appointment/select-location', [AppointmentController::class, 'selectLocation'])
        ->name('appointment.selectLocation');

    Route::middleware('throttle:30,1')
        ->post('/appointment/{uuid}', [AppointmentController::class, 'gate'])
        ->name('appointment.gate');

    Route::middleware(['appointmentGatePass'])->group(function (): void {

        Route::get('/appointment/{uuid}/book', [AppointmentController::class, 'form'])
            ->name('appointment.form');

        Route::middleware('throttle:10,1')
            ->post('/appointment/{uuid}/book', [AppointmentController::class, 'store'])
            ->name('appointment.store');
    });

    Route::middleware('throttle:20,1')
        ->get('/appointment/{uuid}/confirmed', [AppointmentController::class, 'confirmed'])
        ->whereUuid('uuid')
        ->name('appointment.confirmed');
});
