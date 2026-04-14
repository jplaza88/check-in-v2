<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

final readonly class LocaleService
{
    public function __construct(
        private BrowserService $browserService,
        private SessionService $sessionService,
    ) {}

    public function getLocale(Request $request): string
    {
        /*
         * Prioritize locale stored in session since the only way it can live in session is when user explicitly
         * changes the language in the navbar.
         */

        return $this->sessionService->getLocale()
            ?? $this->browserService->detectLocale($request)
            ?? config('app.locale');
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function getTranslationsForRoute(string $routeName, string $locale): array
    {
        $allTranslations = $this->loadTranslations($locale);

        $keys = config('translations.' . $routeName, []);

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
        $path = base_path("lang/$locale/messages.php");

        return File::exists($path) ? include $path : [];
    }
}
