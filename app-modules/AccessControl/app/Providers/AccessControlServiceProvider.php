<?php

declare(strict_types=1);

namespace Modules\AccessControl\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;

class AccessControlServiceProvider extends ModuleServiceProvider
{
    use PathNamespace;

    protected string $name = 'AccessControl';
    protected string $nameLower = 'access-control';
    protected array $providers = [
        AuthServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
