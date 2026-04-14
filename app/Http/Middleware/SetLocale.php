<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\LocaleService;
use Closure;
use Illuminate\Http\Request;

/*
 * This class handles the language settings for driver facing pages (check-in, appointment, and dashboard)
 */
final readonly class SetLocale
{
    public function __construct(private LocaleService $localeService) {}

    public function handle(Request $request, Closure $next): mixed
    {
        $locale = $this->localeService->getLocale($request);

        $routeName = $request->route()?->getName() ?? '';

        app()->setLocale($locale);
        inertia()->share('translations', $this->localeService->getTranslationsForRoute($routeName, $locale));

        return $next($request);
    }
}
