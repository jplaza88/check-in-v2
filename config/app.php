<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'UTC'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    'locales' => [
        'en',
        'es',
        'fr',
    ],

    'locales_labels' => [
        'en' => 'English',
        'es' => 'Español',
        'fr' => 'Français',
    ],

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', '')),
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Location Closing Soon Threshold
    |--------------------------------------------------------------------------
    |
    | Defines how many minutes before closing a location is considered
    | "closing soon". When a location falls within this threshold, a
    | "Closing soon" indicator will be displayed in the check-in UI.
    |
    | Default: 30 minutes
    |
    */

    'location_closing_soon_threshold' => 30,

    /*
    |--------------------------------------------------------------------------
    | User Coordinates Session Time-to-Live
    |--------------------------------------------------------------------------
    |
    | Specifies how long (in seconds) the user's coordinates are stored
    | in the server-side session. After this duration, the coordinates
    | will expire and may need to be re-acquired.
    |
    | Default: 60 seconds * 2 (2 minutes)
    |
    */

    'user_coordinates_session_ttl' => 60 * 2,

    /*
    |--------------------------------------------------------------------------
    | User Coordinates Browser Time-to-Live
    |--------------------------------------------------------------------------
    |
    | Specifies how long (in minutes) the user's coordinates are cached
    | in the browser (e.g., localStorage). Once expired, the browser
    | should request fresh location data.
    |
    | Default: 2 minutes
    |
    */

    'user_coordinates_browser_ttl' => 2,

    /*
    |--------------------------------------------------------------------------
    | Distance Calculation
    |--------------------------------------------------------------------------
    |
    | Specifies how long (in minutes) the user's coordinates are cached
    | in the browser (e.g., localStorage). Once expired, the browser
    | should request fresh location data.
    |
    | Default: 2 minutes
    |
    */

    'user_location_distances_ttl' => 2,

    /*
    |--------------------------------------------------------------------------
    | Appointment Location Context
    |--------------------------------------------------------------------------
    |
    | Specifies how long (in minutes) the user's coordinates are cached
    | in the browser (e.g., localStorage). Once expired, the browser
    | should request fresh location data.
    |
    | Default: 2 minutes
    |
    */

    'user_appointment_location_context_ttl' => 2,
];
