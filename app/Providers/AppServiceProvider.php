<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Appointment;
use App\Models\AppointmentSchedule;
use App\Models\AppointmentScheduleOverride;
use App\Models\CheckInSchedule;
use App\Models\CheckInScheduleOverride;
use App\Models\Location;
use App\Schedule\WeeklyScheduleResolver;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use RuntimeException;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerDatabaseEncrypter();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    /**
     * Bind a dedicated encrypter for at-rest column encryption, keyed by
     * DB_ENCRYPTION_KEY rather than APP_KEY so the two can rotate independently.
     */
    private function registerDatabaseEncrypter(): void
    {
        $this->app->singleton('db.encrypter', function (): Encrypter {
            $key = (string) config('app.db_encryption_key');

            if ($key === '') {
                throw new RuntimeException('DB_ENCRYPTION_KEY is not set. Generate one with: php -r "echo \'base64:\'.base64_encode(random_bytes(32));"');
            }

            $encrypter = new Encrypter($this->parseEncryptionKey($key), config('app.cipher'));

            $previousKeys = array_map(
                $this->parseEncryptionKey(...),
                config('app.db_encryption_previous_keys', []),
            );

            return $previousKeys === [] ? $encrypter : $encrypter->previousKeys($previousKeys);
        });
    }

    /**
     * Decode a "base64:"-prefixed key into its raw bytes, mirroring how Laravel
     * parses APP_KEY.
     */
    private function parseEncryptionKey(string $key): string
    {
        return str_starts_with($key, 'base64:')
            ? (string) base64_decode(mb_substr($key, 7), true)
            : $key;
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    private function configureDefaults(): void
    {
        $this->immutableDates();

        $this->defaultTimezone();

        // TODO: Enable once in prod
        // $this->prohibitDestructiveCommands();

        $this->setPasswordDefault();

        $this->aggressivePrefetching();

        $this->autoEagerLoadRelationships();

        $this->forceHttps();

        $this->strictModels();

        $this->unguardModels();

        $this->enforceMorphMaps();

        $this->flushScheduleCacheOnDataChanges();
    }

    /**
     * Invalidate the cached weekly schedule whenever any location or schedule
     * record changes, so admin edits are reflected on the next request.
     */
    private function flushScheduleCacheOnDataChanges(): void
    {
        $models = [
            Location::class,
            CheckInSchedule::class,
            CheckInScheduleOverride::class,
            AppointmentSchedule::class,
            AppointmentScheduleOverride::class,
        ];

        foreach ($models as $model) {
            $model::saved(static function (): void {
                WeeklyScheduleResolver::flushCache();
            });

            $model::deleted(static function (): void {
                WeeklyScheduleResolver::flushCache();
            });
        }
    }

    private function immutableDates(): void
    {
        Date::use(CarbonImmutable::class);
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

    private function enforceMorphMaps(): void
    {
        Relation::morphMap(['appointment' => Appointment::class]);
    }
}
