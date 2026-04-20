<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentSelectRequest;
use Inertia\Response;

final class AppointmentController extends Controller
{
    public function gate(AppointmentSelectRequest $request): Response
    {
        $request->validated();

        return inertia('AppointmentForm', [
            'location' => $request->location,
        ]);
    }
}
