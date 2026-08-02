<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\CheckIn;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

final readonly class DriverCheckIns
{
    public const int PER_PAGE = 15;

    /**
     * @return Paginator<int, CheckIn>
     */
    public function execute(User $user, ?CarbonImmutable $since = null, int $perPage = self::PER_PAGE): Paginator
    {
        return CheckIn::query()
            ->with('location')
            ->where('user_id', $user->id)
            ->when($since, fn (Builder $query): Builder => $query->where('created_at', '>=', $since))
            ->latest()
            ->orderByDesc('id')
            ->simplePaginate($perPage);
    }
}
