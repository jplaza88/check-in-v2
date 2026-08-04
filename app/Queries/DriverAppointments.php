<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Appointment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

final readonly class DriverAppointments
{
    public const int PER_PAGE = 15;

    /**
     * @return Paginator<int, Appointment>
     */
    public function execute(User $user, ?CarbonImmutable $since = null, int $perPage = self::PER_PAGE): Paginator
    {
        return Appointment::query()
            ->with('location')
            ->where('user_id', $user->id)
            ->when($since, fn (Builder $query): Builder => $query->where('scheduled_for', '>=', $since))
            ->orderByDesc('scheduled_for')
            ->orderByDesc('id')
            ->simplePaginate($perPage);
    }
}
