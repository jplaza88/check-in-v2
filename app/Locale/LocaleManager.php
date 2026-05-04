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
        private BrowserManager     $browser,
        private UserSession $session,
    ) {}

    public function getLocale(Request $request): string
    {
        /*
         * Prioritize locale stored in session since the only way it can live in session is when user explicitly
         * changes the language in the navbar.
         */

        return $this->session->getLocale()
            ?? $this->browser->detectLocale($request)
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
     * @return array<string, mixed>
     */
    private function loadTranslations(string $locale): array
    {
        $path = base_path(sprintf('lang/%s/messages.php', $locale));

        return File::exists($path) ? include $path : [];
    }
}
