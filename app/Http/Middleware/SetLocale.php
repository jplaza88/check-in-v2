<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Locale\LocaleManager;
use Closure;
use Illuminate\Http\Request;

/*
 * This class handles the language settings for driver facing pages (check-in, appointment, and account)
 */
final readonly class SetLocale
{
    public function __construct(private LocaleManager $locales) {}

    public function handle(Request $request, Closure $next): mixed
    {
        $locale = $this->locales->getLocale($request);

        $routeName = $request->route()?->getName() ?? '';

        app()->setLocale($locale);
        inertia()->share('translations', $this->locales->getTranslationsForRoute($routeName, $locale));

        return $next($request);
    }
}
