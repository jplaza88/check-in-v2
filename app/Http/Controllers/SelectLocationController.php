<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SelectLocationController extends Controller
{
    public function __invoke(Request $request)
    {
        return Inertia::render('SelectLocation', [
            'locations' => [],
            'context'   => $request->route('context')
        ]);
    }
}
