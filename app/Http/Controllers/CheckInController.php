<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationSelectRequest;
use Inertia\Response;

class CheckInController extends Controller
{
    public function gate(LocationSelectRequest $request): Response
    {
        $validated = $request->validated();

        return inertia('CheckInForm', [
            'location' => $request->getLocation(),
        ]);
    }
}
