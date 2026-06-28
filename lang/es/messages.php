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

    'purchaseOrders' => [
        'label' => 'Número(s) de orden de compra',
        'placeholder' => 'SO-, PO- o PU- seguido de 4-10 dígitos',
        'required' => 'El número de orden de compra es obligatorio.',
        'format' => 'Debe comenzar con SO, PO o PU, seguido de 4-10 dígitos (ej. PO-12345).',
        'duplicate' => 'No se permiten números de orden de compra duplicados.',
        'minOne' => 'Se requiere al menos un número de orden de compra.',
        'maxReached' => 'Puede agregar hasta 10 números de orden de compra por cita.',
        'addMore' => 'Agregar más',
        'removeAriaLabel' => 'Eliminar orden de compra :number',
    ],

    'appointmentConfirmation' => [
        'pageTitle' => 'Cita Confirmada',
        'newAppointment' => 'Nueva cita',
        'confirmed' => '¡Su cita está confirmada!',
        'subheading' => 'Esperamos verle pronto.',
        'reference' => 'Referencia',
        'bookingSummary' => 'Resumen de la cita',
        'locationLabel' => 'Ubicación',
        'dateLabel' => 'Fecha',
        'timeLabel' => 'Hora',
        'poLabel' => 'Número(s) de orden de compra',
        'driverLabel' => 'Conductor',
        'arrivalReminder' => 'Por favor llegue 5-10 minutos antes de la hora de su cita.',
        'needHelp' => '¿Necesita ayuda?',
        'registerHeading' => 'Acelere su próximo registro',
        'registerBody' => 'Cree una cuenta gratuita para guardar su información y registrarse más rápido la próxima vez.',
        'createAccount' => 'Crear cuenta',
        'signIn' => 'Iniciar sesión',
    ],

    'appointmentForm' => [
        'pageTitle' => 'Reservar una cita',
        'newAppointment' => 'Nueva cita',
        'appointmentDetails' => 'Detalles de la cita',
        'driverInformation' => 'Información del conductor',
        'dateLabel' => 'Fecha',
        'dateRequired' => 'Por favor seleccione una fecha.',
        'timeLabel' => 'Hora',
        'timeRequired' => 'Por favor seleccione una hora.',
        'driversNameLabel' => 'Nombre del conductor',
        'driversNamePlaceholder' => 'Nombre completo',
        'driversNameRequired' => 'El nombre del conductor es obligatorio.',
        'driversNameMin' => 'El nombre del conductor debe tener al menos 2 caracteres.',
        'driversNameMax' => 'El nombre es demasiado largo.',
        'cellphoneLabel' => 'Número de celular',
        'cellphonePlaceholder' => '(555) 000-0000',
        'cellphoneRequired' => 'El número de celular es obligatorio.',
        'cellphoneInvalid' => 'Ingrese un número de teléfono válido de 10 dígitos.',
        'cellphoneConsent' => 'Al proporcionar su número, acepta recibir comunicaciones por SMS y correo electrónico. Responda STOP para cancelar.',
        'todaysHours' => 'Horario de hoy',
        'noAvailability' => 'No hay horarios de cita disponibles actualmente. Por favor, vuelva más tarde o comuníquese con la ubicación.',
        'reviewButton' => 'Revisar cita →',
        'submitting' => 'Enviando…',
        'reviewNote' => 'Podrá revisar antes de confirmar.',
        'reviewTitle' => 'Revise su cita',
        'editButton' => 'Editar',
        'submitButton' => 'Confirmar y enviar',
        'open' => 'Abierto',
    ],
];
