<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * The framework's email-verification notification, queued so it never blocks
 * the request that triggered it (registration or an email change).
 */
final class VerifyEmail extends BaseVerifyEmail implements ShouldQueue
{
    use Queueable;
}
