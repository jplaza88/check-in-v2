<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

final class HandleInertiaRequests extends Middleware
{
    private const array PUBLIC_NAV_ROUTES = [
        'appointment.selectLocation',
        'appointment.form',
        'appointment.confirmed',
        'checkIn.selectLocation',
        'checkIn.form',
        // 'checkIn.*',
        // 'appointment.*',
    ];

    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

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
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'commit' => $this->getShortCommit(...),
            'auth' => [
                'user' => $request->user(...),
            ],
            'currentRoute' => $this->getCurrentRouteName(...),
            'userCoordsBrowserTtl' => $this->getUserCoordsBrowserTtl(...),
            'currentLocale' => fn () => app()->getLocale(),
            'localesAvailable' => fn () => config('app.locales'),
            'localesLabels' => fn () => config('app.locales_labels'),
        ];
    }

    /**
     * The short (7-char) deployed commit SHA, or null in local dev.
     */
    private function getShortCommit(): ?string
    {
        if (request()->route()?->getName() !== 'home') {
            return null;
        }

        $commit = config('app.commit');

        return is_string($commit) && $commit !== '' ? mb_substr($commit, 0, 7) : null;
    }

    private function getCurrentRouteName(): ?string
    {
        return in_array(
            request()->route()?->getName(),
            self::PUBLIC_NAV_ROUTES,
            strict: true
        ) ? request()->route()->getName() : null;
    }

    private function getUserCoordsBrowserTtl(): ?int
    {
        return request()->route()?->getName() === 'checkIn.selectLocation' ?
            config('app.user_coordinates_browser_ttl') : null;
    }
}
