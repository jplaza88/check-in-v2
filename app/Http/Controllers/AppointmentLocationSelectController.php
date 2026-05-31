<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Appointment\AppointmentScheduleResolver;
use Inertia\Response;

final class AppointmentLocationSelectController extends Controller
{
    public function __invoke(AppointmentScheduleResolver $resolver): Response
    {
        return inertia('AppointmentSelectLocation', [
            'locations' => $resolver->getLocations(),
        ]);
    }
}
