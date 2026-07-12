<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Schedule\WeeklyScheduleResolver;
use Inertia\Response;

final class ScheduleController extends Controller
{
    public function index(WeeklyScheduleResolver $resolver): Response
    {
        return inertia('Schedule', [
            'locations' => $resolver->getWeeks(),
        ]);
    }
}
