<?php

declare(strict_types=1);

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountSettingsController;
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

    Route::inertia('/privacy', 'PrivacyPolicy')->name('privacy');

    Route::inertia('/terms', 'TermsOfService')->name('terms');

    /**
     * Authenticated driver account
     */
    Route::middleware('auth')->group(function (): void {
        Route::get('/account', [AccountController::class, 'index'])->name('account');
        Route::get('/account/profile', [AccountController::class, 'editProfile'])->name('account.profile');
        Route::get('/account/settings', [AccountSettingsController::class, 'index'])->name('account.settings');

        Route::middleware('throttle:30,1')
            ->patch('/account/settings', [AccountSettingsController::class, 'update'])
            ->name('account.settings.update');

        // Named for the account rather than the settings, because that is what it
        // destroys. Throttled harder than the preference saves: it is irreversible,
        // and the password check makes it worth guessing at.
        Route::middleware('throttle:6,1')
            ->delete('/account', [AccountSettingsController::class, 'destroy'])
            ->name('account.destroy');

        Route::get('/account/history', [AccountController::class, 'history'])
            ->name('account.history');

        Route::get('/account/history/check-in/{uuid}', [AccountController::class, 'showCheckIn'])
            ->whereUuid('uuid')
            ->name('account.history.checkIn');

        Route::get('/account/history/appointment/{uuid}', [AccountController::class, 'showAppointment'])
            ->whereUuid('uuid')
            ->name('account.history.appointment');

        // Rendering is bounded work behind a cache, but still worth throttling.
        Route::middleware('throttle:10,1')->group(function (): void {
            Route::get('/account/history/check-in/{uuid}/pdf', [AccountController::class, 'checkInPdf'])
                ->whereUuid('uuid')
                ->name('account.history.checkIn.pdf');

            Route::get('/account/history/appointment/{uuid}/pdf', [AccountController::class, 'appointmentPdf'])
                ->whereUuid('uuid')
                ->name('account.history.appointment.pdf');
        });

        // Tighter: each one queues a render and sends mail.
        Route::middleware('throttle:5,1')->group(function (): void {
            Route::post('/account/history/check-in/{uuid}/email', [AccountController::class, 'emailCheckInPdf'])
                ->whereUuid('uuid')
                ->name('account.history.checkIn.email');

            Route::post('/account/history/appointment/{uuid}/email', [AccountController::class, 'emailAppointmentPdf'])
                ->whereUuid('uuid')
                ->name('account.history.appointment.email');
        });
    });

    /**
     * Check-In
     */
    Route::get('/check-in/select-location', [CheckInController::class, 'selectLocation'])
        ->name('checkIn.selectLocation');

    Route::middleware(['userCoordinates', 'throttle:30,1'])
        ->post('/check-in/{uuid}', [CheckInController::class, 'gate'])
        ->whereUuid('uuid')
        ->name('checkIn.gate');

    Route::middleware(['checkInGatePass', 'userCoordinates'])->group(function (): void {

        Route::get('/check-in/{uuid}/form', [CheckInController::class, 'form'])
            ->whereUuid('uuid')
            ->name('checkIn.form');

        Route::middleware('throttle:10,1')
            ->post('/check-in/{uuid}/form', [CheckInController::class, 'store'])
            ->whereUuid('uuid')
            ->name('checkIn.store');
    });

    Route::middleware('throttle:20,1')
        ->get('/check-in/{uuid}/confirmed', [CheckInController::class, 'confirmed'])
        ->whereUuid('uuid')
        ->name('checkIn.confirmed');

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
