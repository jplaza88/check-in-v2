<?php

declare(strict_types=1);

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\CheckInDistanceController;
use App\Http\Controllers\CheckInLocationSelectController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

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
Route::post('/check-in/distance', CheckInDistanceController::class)
    ->middleware('throttle:30,1')
    ->name('checkIn.distance');

Route::middleware(['setLocale'])
    ->group(function (): void {

        Route::get('/check-in/select-location', CheckInLocationSelectController::class)
            ->name('checkIn.selectLocation');

        Route::middleware(['userCoordinates', 'throttle:30,1'])
            ->post('/check-in/{uuid}', [CheckInController::class, 'gate'])
            ->whereUuid('uuid')
            ->name('checkIn.form');

        Route::get('/appointment/select-location', [AppointmentController::class, 'selectLocation'])
            ->name('appointment.selectLocation');

        Route::middleware(['throttle:30,1'])
            ->post('/appointment/{uuid}', [AppointmentController::class, 'gate'])
            ->name('appointment.gate');

        Route::get('/appointment/{uuid}/confirmed', [AppointmentController::class, 'confirmed'])
            ->whereUuid('uuid')
            ->name('appointment.confirmed');

        Route::middleware(['appointmentLocation'])->group(function (): void {
            Route::get('/appointment/{uuid}/book', [AppointmentController::class, 'form'])
                ->name('appointment.form');

            Route::post('/appointment/{uuid}/book', [AppointmentController::class, 'store'])
                ->middleware('throttle:10,1')
                ->name('appointment.store');
        });
    });
