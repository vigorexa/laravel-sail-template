<?php

declare(strict_types=1);

namespace Modules\Blog\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;

class BlogFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'Blog';
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
