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
        'tooFar' => 'You must be within :maxDistance miles of :name to check in. You are currently :userDistance mi away. Please try again once you are closer.',
        'locationClosed' => 'This location is currently closed.',
        'invalidLocation' => 'Invalid Location.',
        'outsideHours' => ':name is closed right now. Business hours today are :open - :close. Please come back during business hours.',
        'checkInUnavailable' => 'Check-In Unavailable',
        'outsideExceptionHours' => ':name has special hours today (:open – :close). You’re outside those hours- please check in during that window.',
        'closedForCheckInWithReason' => ':name is not available for check-in right now: :reason',
        'closedForCheckInWithoutReason' => ':name is not available for check-in right now.',
        'closedForCheckInToday' => ':name is closed for check-in today. Please come back during posted hours.',
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

    'appointmentBooking' => [
        'closedForAppointmentsOnDate' => ':name is not available for appointments on :date. Please choose another day.',
        'closedForAppointmentsOnDateWithReason' => '::name is not available for appointments on :date (:reason). Please choose another day.',
        'outsideSpecialHours' => ':name is not open at :time on :date. Special hours that day are :open - :close. Please choose another date or time.',
        'outsideRegularHours' => ':name is not open at :time on :date. Business hours that day are :open - :close. Please choose another date or time.',
        'notAvailableOnDay' => ':name is not available for appointments on :date. Please choose another day.',
        'closedForAppointmentsOnDateWithoutReason' => ':name is not accepting appointments on :date. Please choose another date.',
    ],
];
