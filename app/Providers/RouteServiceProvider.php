<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as LaravelRouteServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class RouteServiceProvider extends LaravelRouteServiceProvider
{
    public function boot(): void
    {
        URL::forceRootUrl(Config::get('app.url'));
        URL::forceScheme(Str::startsWith(Config::get('app.url'), 'https://') ? 'https' : 'http');
    }
}
