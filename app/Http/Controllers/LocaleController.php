<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SessionService;
use Illuminate\Http\RedirectResponse;

final class LocaleController extends Controller
{
    /*
     * Handles the language switch when the user clicks on a locale in the navbar
     * This will persist locale preference for session.
     */
    public function __invoke(SessionService $sessionService, string $locale): RedirectResponse
    {
        $availableLocales = config('app.locales');

        if (! in_array($locale, $availableLocales, true)) {
            return back();
        }

        app()->setLocale($locale);
        $sessionService->setLocale($locale);

        return back();
    }
}
