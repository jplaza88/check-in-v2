<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Bridge\Mailgun\Transport\MailgunHttpTransport;
use Symfony\Component\Mailer\Bridge\Mailgun\Transport\MailgunSmtpTransport;

beforeEach(function (): void {
    config()->set('services.mailgun', [
        'domain' => 'mg.example.com',
        'secret' => 'testing-sending-key',
        'endpoint' => 'api.mailgun.net',
        'scheme' => 'https',
    ]);

    Mail::purge('mailgun');
});

it('defines a mailgun mailer so MAIL_MAILER=mailgun resolves', function (): void {
    expect(config('mail.mailers.mailgun.transport'))->toBe('mailgun');
});

it('delivers over the mailgun http api rather than smtp', function (): void {
    $transport = Mail::mailer('mailgun')->getSymfonyTransport();

    expect($transport)->toBeInstanceOf(MailgunHttpTransport::class);
    expect($transport instanceof MailgunSmtpTransport)->toBeFalse();
});

it('builds the transport from the services.mailgun credentials', function (): void {
    $transport = (string) Mail::mailer('mailgun')->getSymfonyTransport();

    expect($transport)
        ->toContain('mailgun+https')
        ->toContain('api.mailgun.net')
        ->toContain('mg.example.com');
});

it('honours a non-default region endpoint', function (): void {
    config()->set('services.mailgun.endpoint', 'api.eu.mailgun.net');

    Mail::purge('mailgun');

    expect((string) Mail::mailer('mailgun')->getSymfonyTransport())
        ->toContain('api.eu.mailgun.net');
});
