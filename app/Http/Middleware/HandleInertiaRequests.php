<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    private const array PUBLIC_NAV_ROUTES = [
        'appointment.selectLocation',
        'checkin.selectLocation',
        // 'checkin.*',
        // 'appointment.*',
    ];

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // TODO:: Only show route name for public routes for navigation bar purposes
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => fn () => $request->user(),
            ],
            'currentRoute' => fn () => in_array(
                request()->route()?->getName(),
                self::PUBLIC_NAV_ROUTES,
                strict: true
            ) ? request()->route()->getName() : null,
            'currentLocale' => fn () => app()->getLocale(),
            'localesAvailable' => fn () => config('app.locales'),
            'localesLabels' => fn () => config('app.locales_labels'),
        ];
    }
}
