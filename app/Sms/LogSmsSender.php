<?php

declare(strict_types=1);

namespace App\Sms;

use Illuminate\Support\Facades\Log;

/**
 * Writes the message to the log instead of sending it.
 *
 * There is no carrier account yet, so this keeps the whole path exercisable end
 * to end. The number is logged in full: it is the driver's own, already stored
 * in plain text on the users table, and truncating it would make the log
 * useless for checking delivery.
 */
final readonly class LogSmsSender implements SmsSender
{
    public function send(SmsMessage $message): void
    {
        Log::info('SMS not sent (no carrier configured)', [
            'to' => $message->to,
            'body' => $message->body,
            'length' => mb_strlen($message->body),
        ]);
    }
}
