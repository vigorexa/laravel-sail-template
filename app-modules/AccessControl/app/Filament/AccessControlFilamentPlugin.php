<?php

declare(strict_types=1);

namespace Modules\AccessControl\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;

class AccessControlFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'AccessControl';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->discoverResources(
                in: module_path($this->getId(), 'app/Filament/Resources'),
                for: "Modules\\{$this->getId()}\\Filament\\Resources",
            )
            ->discoverPages(
                in: module_path($this->getId(), 'app/Filament/Pages'),
                for: "Modules\\{$this->getId()}\\Filament\\Pages",
            )
            ->discoverWidgets(
                in: module_path($this->getId(), 'app/Filament/Widgets'),
                for: "Modules\\{$this->getId()}\\Filament\\Widgets",
            );
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
