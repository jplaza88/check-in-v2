<?php

declare(strict_types=1);

return [
    'home' => ['publicNavigation', 'home'],
    'schedule' => ['publicNavigation', 'schedule'],
    'contact' => ['publicNavigation', 'contact'],
    'privacy' => ['publicNavigation'],
    'terms' => ['publicNavigation'],
    'login' => ['login'],
    'register' => ['register'],
    'password.request' => ['forgotPassword'],
    'password.reset' => ['resetPassword'],
    'account' => ['publicNavigation', 'account', 'accountNav'],
    'account.profile' => ['publicNavigation', 'accountProfile', 'accountNav'],
    'checkIn.selectLocation' => ['publicNavigation', 'checkInSelectLocation', 'locationRequiredModal'],
    'checkIn.form' => ['publicNavigation', 'checkInForm', 'purchaseOrders'],
    'checkIn.confirmed' => ['publicNavigation', 'checkInConfirmation', 'registerCta'],
    'appointment.selectLocation' => ['publicNavigation', 'appointmentSelectLocation'],
    'appointment.form' => ['publicNavigation', 'appointmentForm', 'purchaseOrders'],
    'appointment.confirmed' => ['publicNavigation', 'appointmentConfirmation', 'registerCta'],
];
