<?php

namespace App\Services;

use Illuminate\Http\Request;

class BrowserService
{
    /*
     * Detect users default language set in their browser.
     */
    public function detectLocale(Request $request): ?string
    {
        // Get the preferred languages from the browser
        $preferredLanguages = $request->getLanguages();

        // The first language in the array is usually the preferred language
        $browserLanguage = reset($preferredLanguages);

        // Use a regular expression to extract only the language code, eg: 'es', or 'fr'
        if (preg_match('/^([a-z]+)/i', $browserLanguage ?: 'en', $matches)) {

            return strtolower($matches[1]);
        }

        return null;
    }
}
