<?php

declare(strict_types=1);

namespace App\Enums;

enum UserHistoryEvent: string
{
    case ProfileUpdated = 'profile-updated';
    case PasswordUpdated = 'password-updated';
    case PasswordReset = 'password-reset';
}
