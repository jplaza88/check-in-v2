<?php

declare(strict_types=1);

namespace App\Locale;

use App\Browser\BrowserManager;
use App\Session\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

final readonly class LocaleManager
{
    public function __construct(
        private BrowserManager $browser,
        private UserSession $session,
    ) {}

    public function getLocale(Request $request): string
    {
        /*
         * The account column wins: it is only ever written when a signed-in driver explicitly picks a language,
         * so it should follow them across devices and outlive any one session. Session comes next for guests,
         * who can only have one by explicitly changing the language in the navbar. Browser detection is a guess,
         * so it ranks below both.
         */

        return $this->sanitize($request->user()?->locale)
            ?? $this->sanitize($this->session->getLocale())
            ?? $this->sanitize($this->browser->detectLocale($request))
            ?? config('app.locale');
    }

    /**
     * Translations are stored in config/translations.php and are keyed by route name.
     *
     * @return array<string, array<string, string>>
     */
    public function getTranslationsForRoute(string $routeName, string $locale): array
    {
        $allTranslations = $this->loadTranslations($locale);

        $keys = config('translations')[$routeName] ?? [];

        $routeTranslations = [];
        foreach ($keys as $key) {
            if (isset($allTranslations[$key])) {
                $routeTranslations[$key] = $allTranslations[$key];
            }
        }

        return $routeTranslations;
    }

    /**
     * Every candidate has to survive this before it is trusted.
     *
     * Browser detection in particular returns whatever language tag the browser sent, so an unsupported one
     * such as 'de' would otherwise be accepted, find no lang/de/messages.php, and render every page with an
     * empty translations array.
     */
    private function sanitize(?string $locale): ?string
    {
        return $locale !== null && in_array($locale, config('app.locales'), true) ? $locale : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function loadTranslations(string $locale): array
    {
        $path = base_path(sprintf('lang/%s/messages.php', $locale));

        return File::exists($path) ? include $path : [];
    }
}
