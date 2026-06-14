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

    'publicNavigation' => [
        'checkIn' => 'Registro',
        'appointment' => 'Cita',
        'login' => 'Iniciar sesión',
        'logout' => 'Cerrar sesión',
        'schedule' => 'Horario',
        'contact' => 'Contacto',
        'services' => 'Servicios',
        'company' => 'Empresa',
        'legal' => 'Aviso legal',
        'privacyPolicy' => 'Política de privacidad',
        'termsOfService' => 'Términos del servicio',
        'allRightsReserved' => 'Todos los derechos reservados',
    ],

    'locationRequiredModal' => [
        'title' => 'Ubicación requerida',
        'message' => "Por favor, asegúrese de que la ubicación esté habilitada en su dispositivo móvil antes de continuar. Cuando se le solicite, toque 'Permitir' para que podamos capturar su ubicación.",
    ],

    'checkInSelectLocation' => [
        'selectACheckInLocation' => 'Seleccione una ubicación para registrarse',
        'closed' => 'Cerrado',
        'open' => 'Abierto',
        'nearest' => 'Más cercano',
        'specialHoursToday' => 'Horario especial hoy',
        'closingSoon' => 'Cierra pronto',
        'closedToday' => 'Cerrado hoy',
        'geolocationErrorMessage' => 'No se pudo obtener tu ubicación. Por favor, habilita el acceso a la ubicación en la configuración de tu navegador y actualiza la página.',
        'geolocationErrorTitle' => 'Acceso a la ubicación denegado',
        'geolocationNotSupportedMessage' => 'La geolocalización no es compatible con este navegador.',
        'geolocationNotSupportedTitle' => 'Navegador no compatible',
        'fetchDistancesErrorTitle' => 'No se pudieron cargar las ubicaciones',
        'fetchDistancesErrorMessage' => 'Ocurrió un error al calcular las distancias. Por favor, actualiza la página e inténtalo de nuevo.',
        'tooFar' => 'Debe estar dentro de :maxDistance millas de :name para registrarse. Actualmente se encuentra a :userDistance mi de distancia. Inténtelo de nuevo cuando esté más cerca.',
        'locationClosed' => 'Esta ubicación está cerrada actualmente.',
        'invalidLocation' => 'Ubicación no válida.',
        'outsideHours' => ':name está cerrado en este momento. El horario de atención hoy es de :open - :close. Por favor regrese durante el horario de atención.',
        'checkInUnavailable' => 'Registro no disponible',
        'outsideExceptionHours' => ':name tiene horario especial hoy: :open - :close. Por favor regrese durante el horario de atención.',
    ],

    'appointmentSelectLocation' => [
        'selectAnAppointmentLocation' => 'Seleccione una ubicación de cita',
        'closed' => 'Cerrado',
        'open' => 'Abierto',
        'nearest' => 'Más cercano',
        'specialHoursToday' => 'Horario especial hoy',
        'closingSoon' => 'Cierra pronto',
        'closedToday' => 'Cerrado hoy',
        'invalidLocation' => 'Ubicación no válida.',
        'selectLocationToContinue' => 'Por favor seleccione una ubicación para continuar.',
    ],

    'appointmentBooking' => [
        'closedForAppointmentsOnDate' => ':name no está disponible para citas el :date. Por favor elija otro día.',
        'closedForAppointmentsOnDateWithReason' => ':name no está disponible para citas el :date (:reason). Por favor elija otro día.',
        'outsideSpecialHours' => ':name no está abierto a las :time el :date. El horario especial ese día es :open - :close. Por favor elija otra fecha u hora.',
        'outsideRegularHours' => ':name no está abierto a las :time el :date. El horario de atención ese día es :open - :close. Por favor elija otra fecha u hora.',
        'notAvailableOnDay' => ':name no está disponible para citas el :date. Por favor elija otro día.',
        'closedForAppointmentsOnDateWithoutReason' => ':name no acepta citas el :date. Por favor elija otra fecha.',
        'outsideBookingWindow' => 'Por favor elija una hora de cita disponible dentro de los próximos :days días.',
    ],
];
