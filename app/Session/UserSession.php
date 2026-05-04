<?php

declare(strict_types=1);

namespace App\Session;

final readonly class UserSession
{
    public function getId(): string
    {
        return session()->getId();
    }

    public function getLocale(): ?string
    {
        return session('locale');
    }

    public function setLocale(string $locale): void
    {
        session(['locale' => $locale]);
    }
}
