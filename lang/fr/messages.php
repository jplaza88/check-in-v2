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
        'termsOfService' => "Conditions d'utilisation",
        'allRightsReserved' => 'Tous droits réservés',
    ],

    'locationRequiredModal' => [
        'title' => 'Localisation requise',
        'message' => "Veuillez vous assurer que la localisation est activée sur votre appareil mobile avant de continuer. Lorsque vous y êtes invité, appuyez sur 'Autoriser' afin que nous puissions accéder à votre position.",
    ],

    'checkInSelectLocation' => [
        'selectACheckInLocation' => "Sélectionnez un lieu d'enregistrement",
        'closed' => 'Fermé',
        'open' => 'Ouvert',
        'nearest' => 'Le plus proche',
        'specialHoursToday' => "Horaires spéciaux aujourd'hui",
        'closingSoon' => 'Ferme bientôt',
        'closedToday' => "Fermé aujourd'hui",
        'geolocationErrorMessage' => "Impossible de récupérer votre position. Veuillez activer l'accès à la localisation dans les paramètres de votre navigateur et actualiser la page.",
        'geolocationErrorTitle' => 'Accès à la localisation refusé',
        'geolocationNotSupportedMessage' => "La géolocalisation n'est pas prise en charge par ce navigateur.",
        'geolocationNotSupportedTitle' => 'Navigateur non pris en charge',
        'fetchDistancesErrorTitle' => 'Impossible de charger les emplacements',
        'fetchDistancesErrorMessage' => 'Une erreur est survenue lors du calcul des distances. Veuillez actualiser la page et réessayer.',
        'tooFar' => 'Vous devez être à moins de :maxDistance miles de :name pour vous enregistrer. Vous êtes actuellement à :userDistance mi. Veuillez réessayer lorsque vous serez plus proche.',
        'locationClosed' => 'Cet emplacement est actuellement fermé.',
        'invalidLocation' => 'Emplacement invalide.',
        'outsideHours' => ":name est actuellement fermé. Les heures d'ouverture aujourd'hui sont de :open à :close. Veuillez revenir pendant les heures d'ouverture.",
        'checkInUnavailable' => 'Enregistrement non disponible',
        'outsideExceptionHours' => ":name a des horaires spéciaux aujourd'hui : :open - :close. Veuillez revenir pendant les heures d'ouverture.",
    ],

    'appointmentSelectLocation' => [
        'selectAnAppointmentLocation' => 'Sélectionnez un lieu de rendez-vous',
        'closed' => 'Fermé',
        'open' => 'Ouvert',
        'nearest' => 'Le plus proche',
        'specialHoursToday' => "Horaires spéciaux aujourd'hui",
        'closingSoon' => 'Ferme bientôt',
        'closedToday' => "Fermé aujourd'hui",
        'invalidLocation' => 'Lieu non valide.',
        'selectLocationToContinue' => 'Veuillez sélectionner un lieu pour continuer.',
    ],

    'appointmentBooking' => [
        'closedForAppointmentsOnDate' => ":name n'est pas disponible pour les rendez-vous le :date. Veuillez choisir un autre jour.",
        'closedForAppointmentsOnDateWithReason' => ":name n'est pas disponible pour les rendez-vous le :date (:reason). Veuillez choisir un autre jour.",
        'outsideSpecialHours' => ":name n'est pas ouvert à :time le :date. Les horaires spéciaux ce jour-là sont :open - :close. Veuillez choisir une autre date ou heure.",
        'outsideRegularHours' => ":name n'est pas ouvert à :time le :date. Les heures d'ouverture ce jour-là sont :open - :close. Veuillez choisir une autre date ou heure.",
        'notAvailableOnDay' => ":name n'est pas disponible pour les rendez-vous le :date. Veuillez choisir un autre jour.",
        'closedForAppointmentsOnDateWithoutReason' => ":name n'accepte pas de rendez-vous le :date. Veuillez choisir une autre date.",
        'outsideBookingWindow' => 'Veuillez choisir un créneau de rendez-vous disponible dans les :days prochains jours.',
    ],

    'purchaseOrders' => [
        'label' => 'Numéro(s) de bon de commande',
        'placeholder' => 'ex. PO-00123 ou SO-00456',
        'required' => 'Le numéro de bon de commande est obligatoire.',
        'format' => 'Doit commencer par un préfixe de 2-3 lettres, un tiret, puis des chiffres (ex. PO-00123).',
        'duplicate' => 'Les numéros de bon de commande en double ne sont pas autorisés.',
        'minOne' => 'Au moins un numéro de bon de commande est requis.',
        'addMore' => 'Ajouter',
        'removeAriaLabel' => 'Supprimer le bon de commande :number',
    ],

    'appointmentForm' => [
        'pageTitle' => 'Prendre un rendez-vous',
        'newAppointment' => 'Nouveau rendez-vous',
        'appointmentDetails' => 'Détails du rendez-vous',
        'driverInformation' => 'Informations du conducteur',
        'dateLabel' => 'Date',
        'dateRequired' => 'Veuillez sélectionner une date.',
        'timeLabel' => 'Heure',
        'timeRequired' => 'Veuillez sélectionner une heure.',
        'driversNameLabel' => 'Nom du conducteur',
        'driversNamePlaceholder' => 'Nom complet',
        'driversNameRequired' => 'Le nom du conducteur est obligatoire.',
        'driversNameMin' => 'Le nom du conducteur doit comporter au moins 2 caractères.',
        'driversNameMax' => 'Le nom est trop long.',
        'cellphoneLabel' => 'Numéro de téléphone portable',
        'cellphonePlaceholder' => '(555) 000-0000',
        'cellphoneRequired' => 'Le numéro de téléphone portable est obligatoire.',
        'cellphoneInvalid' => 'Entrez un numéro de téléphone américain valide à 10 chiffres.',
        'cellphoneConsent' => 'En fournissant votre numéro, vous acceptez de recevoir des communications par SMS et par courriel. Répondez STOP pour vous désabonner.',
        'todaysHours' => "Horaires d'aujourd'hui",
        'noAvailability' => "Aucun créneau de rendez-vous n'est actuellement disponible. Veuillez réessayer plus tard ou contacter l'établissement.",
        'reviewButton' => 'Vérifier le rendez-vous →',
        'submitting' => 'Envoi en cours…',
        'reviewNote' => 'Vous pourrez vérifier avant de confirmer.',
        'reviewTitle' => 'Vérifiez votre rendez-vous',
        'editButton' => 'Modifier',
        'submitButton' => 'Confirmer et envoyer',
        'open' => 'Ouvert',
    ],
];
