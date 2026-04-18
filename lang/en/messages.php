<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | App Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    // Header navigation bar & footer navigation bar
    'publicNavigation' => [
        'checkIn' => 'Check-In',
        'appointment' => 'Appointment',
        'login' => 'Login',
        'logout' => 'Logout',
        'schedule' => 'Schedule',
        'contact' => 'Contact',
        'services' => 'Services',
        'company' => 'Company',
        'legal' => 'Legal',
        'privacyPolicy' => 'Privacy Policy',
        'termsOfService' => 'Terms of Service',
        'allRightsReserved' => 'All Rights Reserved',
    ],

    // Modal displayed when selecting location for either check-in or appointment
    'locationRequiredModal' => [
        'title' => 'Location required',
        'message' => "Please make sure that you have your location enabled on your mobile device before proceeding. When prompted, please tap 'Allow' for us to be able to capture your location.",
    ],

    /*
     * Page: Select a check-in location
     * IMPORTANT: The array key should match the route name
     */
    'checkInSelectLocation' => [
        'selectACheckInLocation' => 'Select a check-in location',
        'closed' => 'Closed',
        'open' => 'Open',
        'nearest' => 'Nearest',
        'specialHoursToday' => 'Special hours today',
        'closingSoon' => 'Closing soon',
        'closedToday' => 'Closed today',
        'geolocationErrorMessage' => 'Unable to retrieve your location. Please enable location access in your browser settings and refresh the page.',
        'geolocationErrorTitle' => 'Location access denied',
        'geolocationNotSupportedMessage' => 'Geolocation is not supported by this browser.',
        'geolocationNotSupportedTitle' => 'Browser not supported',
        'fetchDistancesErrorTitle' => 'Unable to load locations',
        'fetchDistancesErrorMessage' => 'Something went wrong while calculating distances. Please refresh the page and try again.',
        'tooFar' => 'You are too far from this location.',
        'locationClosed' => 'This location is currently closed.',
        'invalidLocation' => 'Invalid Location.',
    ],

    'appointmentSelectLocation' => [
        'selectAnAppointmentLocation' => 'Select an appointment location',
        'closed' => 'Closed',
        'open' => 'Open',
        'nearest' => 'Nearest',
        'specialHoursToday' => 'Special hours today',
        'closingSoon' => 'Closing soon',
        'closedToday' => 'Closed today',
    ],
];
