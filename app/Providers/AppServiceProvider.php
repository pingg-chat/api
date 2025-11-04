<?php

declare(strict_types = 1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configPasswordRules();
        $this->configUrls();
        $this->configModels();
        $this->configDates();
        $this->configCommands();
        $this->configResources();
    }

    protected function configPasswordRules(): void
    {
        Password::defaults(function () {
            $password = Password::min(8);

            if (app()->isProduction()) {
                $password
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised();
            }

            return $password;
        });
    }

    protected function configUrls(): void
    {
        URL::forceScheme('https');
    }

    protected function configModels(): void
    {
        Model::unguard();
        Model::shouldBeStrict();
        Model::preventLazyLoading(! app()->isProduction());
    }

    protected function configDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    protected function configCommands(): void
    {
        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );
    }

    public function configResources(): void
    {
        JsonResource::withoutWrapping();
    }
}
