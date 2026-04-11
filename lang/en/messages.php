<?php

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
    ],

    'appointmentSelectLocation' => [
        'selectAnAppointmentLocation' => 'Select an appointment location',
        'closed' => 'Closed',
        'open' => 'Open',
        'nearest' => 'Nearest',
    ],
];
