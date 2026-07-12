<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Response;

final class ContactController extends Controller
{
    public function index(): Response
    {
        return inertia('Contact', [
            'phone' => config('app.contact_phone'),
            'email' => config('app.contact_email'),
        ]);
    }
}
