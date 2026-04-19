<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LocationSelectRequest;
use Inertia\Response;

final class CheckInController extends Controller
{
    public function gate(LocationSelectRequest $request): Response
    {
        $request->validated();

        return inertia('CheckInForm', [
            'location' => $request->location,
        ]);
    }
}
