<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class LocaleService
{
    /**
     * @return array<string, string>
     */
    public function getTranslationsForRoute(string $routeName, string $locale): array
    {
        $routeTranslationMap = [
            'checkIn.selectLocation' => ['publicNavigation', 'checkInSelectLocation', 'locationRequiredModal'],
            'appointment.selectLocation' => ['publicNavigation', 'appointmentSelectLocation'],
        ];

        $allTranslations = [];
        $translationsPath = base_path("lang/$locale/messages.php");
        if (File::exists($translationsPath)) {
            $allTranslations = include $translationsPath;
        }

        $keys = $routeTranslationMap[$routeName] ?? [];

        $routeTranslations = [];
        foreach ($keys as $key) {
            if (isset($allTranslations[$key])) {
                $routeTranslations[$key] = $allTranslations[$key];
            }
        }

        return $routeTranslations;
    }
}
