<?php

namespace App\Http\Middleware;

use App\Services\BrowserService;
use App\Services\SessionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

/*
 * This class handles the language settings for driver facing pages (check-in, appointment, and dashboard)
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): mixed
    {
        $browserService = new BrowserService;
        $sessionService = new SessionService;

        $sessionLocale = $sessionService->getLocale();
        $browserLocale = $browserService->detectLocale($request);

        /*
         * Prioritize locale stored in session since the only way it can live in session is when user explicitly
         * changes the language in the navbar.
         */
        $locale = $sessionLocale ?? $browserLocale ?? config('app.locale');

        // Pull translations. Even english has to be pulled
        $allTranslations = [];
        $translationsPath = base_path("lang/$locale/messages.php");
        if (File::exists($translationsPath)) {
            $allTranslations = include $translationsPath;
        }

        $routeName = $request->route()?->getName() ?? '';

        $userTranslations = $this->fetchTranslations($routeName, $allTranslations);

        // Set the app language and send translations to the front-end
        app()->setLocale($locale);
        inertia()->share('translations', $userTranslations);

        return $next($request);
    }


    /**
     * @param string $routeName
     * @param array<string, string> $allTranslations
     * @return array<string, string>
     */
    private function fetchTranslations(string $routeName, array $allTranslations): array
    {
        $routeTranslationMap = [
            'checkIn.selectLocation' => ['publicNavigation', 'checkInSelectLocation', 'locationRequiredModal'],
            'appointment.selectLocation' => ['publicNavigation', 'appointmentSelectLocation'],
        ];

        $keys = $routeTranslationMap[$routeName] ?? [];

        $userTranslations = [];
        foreach ($keys as $key) {
            if (isset($allTranslations[$key])) {
                $userTranslations[$key] = $allTranslations[$key];
            }
        }

        return $userTranslations;
    }
}
