<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationSelectRequest;
use Inertia\Response;

class AppointmentController extends Controller
{
    public function gate(LocationSelectRequest $request): Response
    {
        $request->validated();

        return inertia('AppointmentForm', [
            'location' => $request->getLocation(),
        ]);
    }
}
