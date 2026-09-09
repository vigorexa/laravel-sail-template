<?php

declare(strict_types=1);

namespace Modules\Blog\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;

class BlogServiceProvider extends ModuleServiceProvider
{
    use PathNamespace;

    protected string $name = 'Blog';

    protected string $nameLower = 'blog';

    protected array $providers = [
        AuthServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
