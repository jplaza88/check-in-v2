<?php

namespace App\Services;

class SessionService
{
    public function getLocale(): ?string
    {
        return session('locale');
    }

    public function setLocale(string $locale): void
    {
        session(['locale' => $locale]);
    }
}
