<?php

declare(strict_types=1);

namespace App\Enums;

enum CheckInErpStatus: string
{
    case Pending = 'pending';
    case Synced = 'synced';
    case Failed = 'failed';
}
