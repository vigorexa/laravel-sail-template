<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\GenerateModuleScaffolding;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (App::isLocal() && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            App::register(\Laravel\Telescope\TelescopeServiceProvider::class);
            App::register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole() && App::isLocal()) {
            Event::listen('modules.*.created', GenerateModuleScaffolding::class);
        }

        Password::defaults(function () {
            if (App::isLocal()) {
                return Password::min(6);
            }

            return Password::min(8)->letters()->mixedCase()->numbers()->symbols();
        });
    }
}
