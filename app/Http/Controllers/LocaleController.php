<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Session\UserSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class LocaleController extends Controller
{
    /*
     * Handles the language switch when the user clicks on a locale in the navbar or on the settings page.
     * Signed-in drivers get it written to their account so it follows them to other devices; guests keep
     * it for the session, and adopt it onto their account if they later sign in.
     */
    public function __invoke(Request $request, UserSession $session, string $locale): RedirectResponse
    {
        $availableLocales = config('app.locales');

        if (! in_array($locale, $availableLocales, true)) {
            return back();
        }

        app()->setLocale($locale);
        $request->user()?->update(['locale' => $locale]);
        $session->setLocale($locale);

        return back();
    }
}
