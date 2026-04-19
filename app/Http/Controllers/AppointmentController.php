<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LocationSelectRequest;
use Inertia\Response;

final class AppointmentController extends Controller
{
    public function gate(LocationSelectRequest $request): Response
    {
        $request->validated();

        return inertia('AppointmentForm', [
            'location' => $request->location,
        ]);
    }
}
