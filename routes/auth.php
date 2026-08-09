<?php

declare(strict_types=1);

use App\Auth\HomeRedirect;
use App\Auth\RegistrationGate;
use Illuminate\Http\Request;
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
    Route::get('/login', fn () => inertia('Auth/Login', [
        'status' => session('status'),
    ]))->name('login');

    Route::get('/register', fn (RegistrationGate $gate) => inertia('Auth/Register', [
        'defaultName' => $gate->name(),
    ]))
        ->middleware('registrationAllowed')
        ->name('register');

    // Request a reset link. Fortify owns POST /forgot-password (password.email).
    Route::get('/forgot-password', fn () => inertia('Auth/ForgotPassword', [
        'status' => session('status'),
    ]))->name('password.request');

    // Landing page for the emailed reset link. Fortify builds that link from the
    // password.reset named route, so it must exist even with views => false.
    // Fortify owns POST /reset-password (password.update).
    Route::get('/reset-password/{token}', fn (string $token) => inertia('Auth/ResetPassword', [
        'token' => $token,
        'email' => request('email'),
    ]))->name('password.reset');
});

/*
| Signed in but not yet verified. This is where the "verified" middleware sends
| drivers, and it has to exist for that middleware to work at all: Fortify runs
| with views => false, so it registers only the POST resend and the signed GET
| link, never this prompt. Deliberately not behind "verified" itself.
*/
Route::middleware(['auth', 'setLocale'])->group(function (): void {
    Route::get('/email/verify', fn (Request $request) => $request->user()->hasVerifiedEmail()
        ? redirect()->intended(HomeRedirect::for())
        : inertia('Auth/VerifyEmail', ['status' => session('status')]))
        ->name('verification.notice');
});
