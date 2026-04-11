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
    ],

    'appointmentSelectLocation' => [
        'selectAnAppointmentLocation' => 'Seleccione una ubicación de cita',
        'closed' => 'Cerrado',
        'open' => 'Abierto',
        'nearest' => 'Más cercano',
        'specialHoursToday' => 'Horario especial hoy',
        'closingSoon' => 'Cierra pronto',
        'closedToday' => 'Cerrado hoy',
    ],
];
