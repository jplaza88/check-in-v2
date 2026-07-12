<?php

declare(strict_types=1);

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\CheckInDistanceController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->middleware('setLocale')->name('home');

Route::middleware('setLocale')
    ->get('/schedule', [ScheduleController::class, 'index'])
    ->name('schedule');

Route::middleware('setLocale')
    ->get('/contact', [ContactController::class, 'index'])
    ->name('contact');

Route::get('/about', static function (): string|false {
    ob_start();
    phpinfo();

    return ob_get_clean();
});

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

Route::middleware('setLocale')->group(function (): void {

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
