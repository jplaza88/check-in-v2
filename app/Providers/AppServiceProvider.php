<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    private function configureDefaults(): void
    {
        $this->immutableDates();

        $this->defaultTimezone();

        $this->prohibitDestructiveCommands();

        $this->setPasswordDefault();

        $this->aggressivePrefetching();

        $this->autoEagerLoadRelationships();

        $this->forceHttps();

        $this->strictModels();

        $this->unguardModels();
    }

    private function immutableDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    private function prohibitDestructiveCommands(): void
    {
        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );
    }

    private function setPasswordDefault(): void
    {
        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    private function aggressivePrefetching(): void
    {
        Vite::useAggressivePrefetching();
    }

    private function autoEagerLoadRelationships(): void
    {
        Model::automaticallyEagerLoadRelationships();
    }

    private function forceHttps(): void
    {
        if (app()->isProduction()) {
            URL::forceHttps();
        }
    }

    private function strictModels(): void
    {
        if (! app()->isProduction()) {
            Model::shouldBeStrict();
        }
    }

    private function unguardModels(): void
    {
        Model::unguard();
    }

    private function defaultTimezone(): void
    {
        date_default_timezone_set(config('app.timezone'));
    }
}
