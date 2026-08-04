<?php

declare(strict_types=1);

namespace App\Enums;

enum HistoryTab: string
{
    case CheckIns = 'check-ins';
    case Appointments = 'appointments';
}
