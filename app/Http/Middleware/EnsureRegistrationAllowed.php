<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Auth\RegistrationGate;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class EnsureRegistrationAllowed
{
    public function __construct(private RegistrationGate $gate) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->gate->isAllowed()) {
            return to_route('home');
        }

        return $next($request);
    }
}
