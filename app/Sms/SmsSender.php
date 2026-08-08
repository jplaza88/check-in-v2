<?php

declare(strict_types=1);

namespace App\Sms;

/**
 * The seam a real carrier plugs into.
 *
 * Only {@see LogSmsSender} implements this today. Wiring FlowRoute up means a
 * second implementation, a `services.flowroute` config block and one changed
 * line in AppServiceProvider; no notification or model code has to move.
 */
interface SmsSender
{
    public function send(SmsMessage $message): void;
}
