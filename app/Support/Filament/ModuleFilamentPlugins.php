<?php

declare(strict_types=1);

namespace App\Support\Filament;

use Filament\Contracts\Plugin;
use Illuminate\Support\Facades\App;
use Nwidart\Modules\Facades\Module;

final class ModuleFilamentPlugins
{
    /**
     * @return array<int, Plugin>
     */
    public static function discover(): array
    {
        return collect(Module::allEnabled())
            ->map(fn ($module) => data_get($module->get('filament'), 'plugin'))
            ->filter(fn (?string $class) => is_string($class) && $class !== '')
            ->filter(fn (string $class) => class_exists($class) && is_subclass_of($class, Plugin::class))
            ->map(fn (string $class) => App::make($class))
            ->values()
            ->all();
    }
}
