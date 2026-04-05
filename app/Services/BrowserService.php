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

        // Use a regular expression to extract only the language code
        if (preg_match('/^([a-z]+)/i', $browserLanguage, $matches)) {
            $languageCode = strtolower($matches[1]);

            return $languageCode;
        }

        return null;
    }
}
