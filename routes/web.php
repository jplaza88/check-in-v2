<?php

declare(strict_types=1);

use App\Http\Controllers\LocaleController;
use App\Http\Controllers\SelectLocationController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::get('/about', static function () {
    ob_start();
    phpinfo();

    return ob_get_clean();
});

$locales = implode('|', config('app.locales'));

Route::prefix('locale')->group(function () use ($locales) {
    Route::post('/{locale}', LocaleController::class)
        ->where('locale', $locales)
        ->name('localeSwitch');
});

Route::middleware(['setLocale'])
    ->group(function () {

        Route::get('/check-in/select-location', SelectLocationController::class)
            ->defaults('context', 'checkin')
            ->name('checkIn.selectLocation');

        Route::get('/appointment/select-location', SelectLocationController::class)
            ->defaults('context', 'appointment')
            ->name('appointment.selectLocation');
    });
