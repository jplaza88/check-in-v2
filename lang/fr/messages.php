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
        'checkIn' => 'Enregistrement',
        'appointment' => 'Rendez-vous',
        'login' => 'Connexion',
        'logout' => 'Déconnexion',
        'schedule' => 'Horaire',
        'contact' => 'Contact',
        'services' => 'Services',
        'company' => 'Entreprise',
        'legal' => 'Mentions légales',
        'privacyPolicy' => 'Politique de confidentialité',
        'termsOfService' => 'Conditions d’utilisation',
        'allRightsReserved' => 'Tous droits réservés',
    ],

    'locationRequiredModal' => [
        'title' => 'Localisation requise',
        'message' => "Veuillez vous assurer que la localisation est activée sur votre appareil mobile avant de continuer. Lorsque vous y êtes invité, appuyez sur 'Autoriser' afin que nous puissions accéder à votre position.",
    ],

    'checkInSelectLocation' => [
        'selectACheckInLocation' => 'Sélectionnez un lieu d’enregistrement',
        'closed' => 'Fermé',
        'open' => 'Ouvert',
        'nearest' => 'Le plus proche',
        'specialHoursToday' => 'Horaires spéciaux aujourd’hui',
        'closingSoon' => 'Ferme bientôt',
        'closedToday' => 'Fermé aujourd’hui',
        'geolocationErrorMessage' => 'Impossible de récupérer votre position. Veuillez activer l’accès à la localisation dans les paramètres de votre navigateur et actualiser la page.',
        'geolocationErrorTitle' => 'Accès à la localisation refusé',
        'geolocationNotSupportedMessage' => 'La géolocalisation n’est pas prise en charge par ce navigateur.',
        'geolocationNotSupportedTitle' => 'Navigateur non pris en charge',
        'fetchDistancesErrorTitle' => 'Impossible de charger les emplacements',
        'fetchDistancesErrorMessage' => 'Une erreur est survenue lors du calcul des distances. Veuillez actualiser la page et réessayer.',
        'tooFar' => 'Vous devez être à moins de :maxDistance miles de :name pour vous enregistrer. Vous êtes actuellement à :userDistance mi. Veuillez réessayer lorsque vous serez plus proche.',
        'locationClosed' => 'Cet emplacement est actuellement fermé.',
        'invalidLocation' => 'Emplacement invalide.',
        'outsideHours' => ':name est actuellement fermé. Les heures d\'ouverture aujourd\'hui sont de :open à :close. Veuillez revenir pendant les heures d\'ouverture.',
        'checkInUnavailable' => 'Enregistrement non disponible',
        'outsideExceptionHours' => ':name a des horaires spéciaux aujourd\'hui : :open - :close. Veuillez revenir pendant les heures d\'ouverture.',
    ],

    'appointmentSelectLocation' => [
        'selectAnAppointmentLocation' => 'Sélectionnez un lieu de rendez-vous',
        'closed' => 'Fermé',
        'open' => 'Ouvert',
        'nearest' => 'Le plus proche',
        'specialHoursToday' => 'Horaires spéciaux aujourd’hui',
        'closingSoon' => 'Ferme bientôt',
        'closedToday' => 'Fermé aujourd’hui',
    ],
];
