<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\CheckIn\CheckInScheduleResolver;
use Inertia\Response;

final class CheckInLocationSelectController extends Controller
{
    public function __invoke(CheckInScheduleResolver $resolver): Response
    {
        return inertia('CheckInSelectLocation', [
            'locations' => $resolver->getLocations(),
        ]);
    }
}
