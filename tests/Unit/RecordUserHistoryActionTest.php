<?php

declare(strict_types=1);

use App\Actions\RecordUserHistoryAction;
use App\Enums\UserHistoryEvent;
use App\Models\User;

function historyActionUser(): User
{
    return User::query()->create([
        'name' => 'John Driver',
        'email' => 'john@example.com',
        'password' => 'secret-password',
    ]);
}

it('drops the values of sensitive attributes but keeps the fact they changed', function (string $attribute): void {
    $user = historyActionUser();

    $history = resolve(RecordUserHistoryAction::class)->handle($user, UserHistoryEvent::ProfileUpdated, [
        'name' => ['from' => 'John', 'to' => 'Johnny'],
        $attribute => ['from' => 'the-old-secret', 'to' => 'the-new-secret'],
    ]);

    expect($history->changes[$attribute])->toBe(['changed' => true])
        ->and($history->changes['name'])->toBe(['from' => 'John', 'to' => 'Johnny']);
})->with([
    'drivers_license_number',
    'password',
    'remember_token',
    'two_factor_secret',
    'two_factor_recovery_codes',
]);

it('stores a null changes payload when there is nothing to diff', function (): void {
    $user = historyActionUser();

    $history = resolve(RecordUserHistoryAction::class)->handle($user, UserHistoryEvent::PasswordUpdated);

    expect($history->changes)->toBeNull()
        ->and($history->event)->toBe(UserHistoryEvent::PasswordUpdated)
        ->and($history->user_id)->toBe($user->id);
});
