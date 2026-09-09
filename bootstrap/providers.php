<?php

declare(strict_types=1);

use App\Providers\AdminPanelProvider;
use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\HorizonServiceProvider;
use App\Providers\RouteServiceProvider;

return [
    AdminPanelProvider::class,
    AppServiceProvider::class,
    AuthServiceProvider::class,
    HorizonServiceProvider::class,
    RouteServiceProvider::class,
];
