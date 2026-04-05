<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Sleep;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
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
    protected function configureDefaults(): void
    {
        $this->immutableDates();

        $this->prohibitDestructiveCommands();

        $this->setPasswordDefault();

        $this->aggressivePrefetching();

        $this->autoEagerLoadRelationships();

        $this->fakeSleep();

        $this->forceHttps();

        $this->preventStrayRequests();

        $this->strictModels();

        $this->unguardModels();
    }

    public function immutableDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    public function prohibitDestructiveCommands(): void
    {
        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );
    }

    public function setPasswordDefault(): void
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

    public function aggressivePrefetching(): void
    {
        Vite::useAggressivePrefetching();
    }

    public function autoEagerLoadRelationships(): void
    {
        Model::automaticallyEagerLoadRelationships();
    }

    public function fakeSleep(): void
    {
        Sleep::fake();
    }

    public function forceHttps(): void
    {
        URL::forceHttps();
    }

    public function preventStrayRequests(): void
    {
        Http::preventStrayRequests();
    }

    public function strictModels(): void
    {
        Model::shouldBeStrict();
    }

    public function unguardModels(): void
    {
        Model::unguard();
    }
}
