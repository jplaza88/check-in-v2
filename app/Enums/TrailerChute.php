<?php

declare(strict_types=1);

namespace App\Enums;

enum TrailerChute: string
{
    case NotApplicable = 'n/a';
    case CenterChute = 'center-chute';
    case SideChute = 'side-chute';
}
