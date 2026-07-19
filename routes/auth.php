<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| View routes for the auth screens. Fortify (config: views => false) owns the
| POST endpoints (login, logout, register, password reset, etc.); the GET
| pages that render our own UI live here. Loaded with the "web" and "setLocale"
| middleware via bootstrap/app.php.
|
*/

Route::middleware(['guest', 'setLocale'])->group(function (): void {
    Route::inertia('/login', 'Auth/Login')->name('login');
    Route::inertia('/register', 'Auth/Register')
        ->middleware('registrationAllowed')
        ->name('register');
});
