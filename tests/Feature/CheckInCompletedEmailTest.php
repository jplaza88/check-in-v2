<?php

declare(strict_types=1);

use App\Actions\CompleteCheckInAction;
use App\CheckIn\CheckInScheduleResolver;
use App\Mail\CheckInCompleted;
use App\Models\Location;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Mail;

/**
 * @param  array<string, mixed>  $config
 */
function createCheckInLocation(array $config = []): Location
{
    return Location::factory()->create(['config' => $config]);
}

function completeCheckIn(Location $location): void
{
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
    ], $dto);
}

beforeEach(function (): void {
    Date::setTestNow(Date::today('UTC')->setTime(8, 0));
});

it('queues the shipping department notification to the configured addresses', function (): void {
    Mail::fake();

    $location = createCheckInLocation([
        'checkin' => [
            'email_addresses' => ['shipping@example.com', 'ops@example.com'],
        ],
    ]);

    completeCheckIn($location);

    Mail::assertQueued(
        CheckInCompleted::class,
        fn (CheckInCompleted $mail): bool => $mail->hasTo('shipping@example.com')
            && $mail->hasTo('ops@example.com'),
    );
});

it('does not queue a notification when the location has no configured addresses', function (): void {
    Mail::fake();

    $location = createCheckInLocation();

    completeCheckIn($location);

    Mail::assertNotQueued(CheckInCompleted::class);
});

it('ignores invalid addresses in the location config', function (): void {
    Mail::fake();

    $location = createCheckInLocation([
        'checkin' => [
            'email_addresses' => ['not-an-email', 'shipping@example.com'],
        ],
    ]);

    completeCheckIn($location);

    Mail::assertQueued(
        CheckInCompleted::class,
        fn (CheckInCompleted $mail): bool => $mail->hasTo('shipping@example.com')
            && ! $mail->hasTo('not-an-email'),
    );
});

it('renders the check-in details in the notification', function (): void {
    Mail::fake();

    $location = createCheckInLocation([
        'checkin' => [
            'email_addresses' => ['shipping@example.com'],
        ],
    ]);

    completeCheckIn($location);

    Mail::assertQueued(CheckInCompleted::class, function (CheckInCompleted $mail): bool {
        $html = $mail->render();

        return str_contains($html, $mail->checkIn->reference_number)
            && str_contains($html, $mail->checkIn->location->name)
            && str_contains($html, 'Acme Produce')
            && str_contains($html, 'Phoenix, Arizona, US')
            && str_contains($html, 'SO-00001')
            && str_contains($html, 'John Doe')
            && str_contains($html, '+1 (201) 555-0123');
    });
});
