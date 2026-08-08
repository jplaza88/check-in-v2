<?php

declare(strict_types=1);

use App\Actions\CompleteCheckInAction;
use App\Actions\SendCheckInCopyAction;
use App\CheckIn\CheckInScheduleResolver;
use App\Enums\NotificationChannel;
use App\Models\CheckIn;
use App\Models\Location;
use App\Models\User;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\CheckInCopy;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Notification;

/**
 * @param  array<string, mixed>  $attributes
 */
function copyDriver(array $attributes = []): User
{
    return User::query()->create([
        'name' => 'John Driver',
        'email' => 'driver@example.com',
        'password' => 'secret-password',
        ...$attributes,
    ]);
}

/**
 * @param  array<string, mixed>  $config
 */
function checkInAs(?User $user, array $config = []): void
{
    // Signed in for real: CreateCheckInAction stamps the check-in's locale from
    // the request user, so a driver passed in by hand would record the wrong one.
    if ($user instanceof User) {
        test()->actingAs($user);
    }

    $location = Location::factory()->create(['config' => $config]);
    $dto = resolve(CheckInScheduleResolver::class)->buildDTO($location, now());

    resolve(CompleteCheckInAction::class)->handle([
        'customer' => 'Acme Produce',
        'destination_city' => 'Phoenix',
        'destination_state' => 'Arizona',
        'destination_country' => 'US',
        'po_numbers' => ['SO-00001'],
        'truck_name' => 'Truck 42',
        'truck_plate' => 'ABC1234',
        'trailer_plate' => 'XYZ9876',
        'trailer_chute' => 'center-chute',
        'drivers_name' => 'John Doe',
        'drivers_cellphone' => '+12015550123',
        'drivers_license_number' => 'D12345678',
    ], $dto, $user);
}

beforeEach(function (): void {
    Date::setTestNow(Date::today('UTC')->setTime(8, 0));
    Notification::fake();
});

it('sends the driver their copy by default', function (): void {
    $driver = copyDriver();

    checkInAs($driver);

    Notification::assertSentTo($driver, CheckInCopy::class);
});

/*
 * No form collects an email address or a phone number from a guest, so there is
 * nowhere to send a copy and no preference to read.
 */
it('sends nothing for a guest check-in', function (): void {
    checkInAs(null);

    Notification::assertNothingSent();
});

it('sends nothing when the driver has turned the copy off', function (): void {
    $driver = copyDriver(['notify_check_in_copy' => false]);

    checkInAs($driver);

    Notification::assertNothingSent();
});

it('sends on the channels the driver chose', function (NotificationChannel $channel, array $expected): void {
    $driver = copyDriver([
        'cellphone' => '+12015550123',
        'notification_channel' => $channel,
    ]);

    checkInAs($driver);

    Notification::assertSentTo(
        $driver,
        CheckInCopy::class,
        fn (CheckInCopy $notification, array $channels): bool => $channels === $expected,
    );
})->with([
    'email' => [NotificationChannel::Email, ['mail']],
    'text' => [NotificationChannel::Sms, [SmsChannel::class]],
    'both' => [NotificationChannel::Both, ['mail', SmsChannel::class]],
]);

/*
 * Picking Text before adding a number would otherwise drop the copy on the
 * floor. Falling back to email keeps the confirmation arriving.
 */
it('falls back to email when the driver picked text but has no number on file', function (): void {
    $driver = copyDriver(['notification_channel' => NotificationChannel::Sms]);

    checkInAs($driver);

    Notification::assertSentTo(
        $driver,
        CheckInCopy::class,
        fn (CheckInCopy $notification, array $channels): bool => $channels === ['mail'],
    );
});

/*
 * Driven through the send action rather than a full check-in: the locale a
 * check-in records comes from the HTTP request, so the language to assert on is
 * the one already stamped on the row.
 */
it('carries the locale stamped on the check-in', function (): void {
    $driver = copyDriver();
    $checkIn = CheckIn::factory()->forUser($driver)->create(['locale' => 'es']);

    resolve(SendCheckInCopyAction::class)->handle($checkIn);

    Notification::assertSentTo(
        $driver,
        CheckInCopy::class,
        fn (CheckInCopy $notification): bool => $notification->locale === 'es',
    );
});

it('renders the text message with the reference and a link to the record', function (): void {
    $driver = copyDriver([
        'cellphone' => '+12015550123',
        'notification_channel' => NotificationChannel::Sms,
    ]);

    checkInAs($driver);

    Notification::assertSentTo($driver, CheckInCopy::class, function (CheckInCopy $notification) use ($driver): bool {
        $message = $notification->toSms($driver);
        $checkIn = $notification->checkIn;

        return str_contains($message->body, $checkIn->reference_number)
            && str_contains($message->body, route('account.history.checkIn', $checkIn->uuid))
            && $message->to === '+12015550123';
    });
});

/*
 * A second segment is billed separately and these bodies are translated, so the
 * budget has to hold in every locale. Nothing in the message is variable-width
 * any more, but the link grows with the configured domain, so this is the test
 * that fails if someone lengthens a string or moves the app to a longer host.
 */
it('keeps the text message inside one segment in every locale', function (string $locale): void {
    App::setLocale($locale);

    $driver = copyDriver(['cellphone' => '+12015550123']);
    $checkIn = CheckIn::factory()->forUser($driver)->create();

    $body = new CheckInCopy($checkIn)->toSms($driver)->body;

    expect(mb_strlen($body))->toBeLessThanOrEqual(160)
        ->and($body)->toContain($checkIn->reference_number);
})->with(['en', 'es', 'fr']);
