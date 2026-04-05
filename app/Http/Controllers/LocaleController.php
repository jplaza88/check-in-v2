<?php

namespace App\Http\Controllers;

use App\Services\SessionService;

class LocaleController extends Controller
{
    /*
     * Handles the language switch when the user clicks on a locale in the navbar
     * This will persist locale preference for session.
     */
    public function __invoke(SessionService $sessionService, string $locale)
    {
        $availableLocales = config('app.locales');

        if (! in_array($locale, $availableLocales, true)) {
            return redirect()->back();
        }

        app()->setLocale($locale);
        $sessionService->setLocale($locale);

        return redirect()->back();
    }
}
