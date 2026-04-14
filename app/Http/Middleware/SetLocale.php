<?php

namespace App\Http\Middleware;

use App\Services\BrowserService;
use App\Services\LocaleService;
use App\Services\SessionService;
use Closure;
use Illuminate\Http\Request;

/*
 * This class handles the language settings for driver facing pages (check-in, appointment, and dashboard)
 */
final readonly class SetLocale
{
    public function __construct(
        private BrowserService $browserService,
        private SessionService $sessionService,
        private LocaleService  $localeService,
    ) {}

    public function handle(Request $request, Closure $next): mixed
    {
        $sessionLocale = $this->sessionService->getLocale();
        $browserLocale = $this->browserService->detectLocale($request);

        /*
         * Prioritize locale stored in session since the only way it can live in session is when user explicitly
         * changes the language in the navbar.
         */
        $locale = $sessionLocale ?? $browserLocale ?? config('app.locale');

        $routeName = $request->route()?->getName() ?? '';

        app()->setLocale($locale);
        inertia()->share('translations', $this->localeService->getTranslationsForRoute($routeName, $locale));

        return $next($request);
    }
}
