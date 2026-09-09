<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Console\Commands\ModuleMakeFilamentPluginCommand;
use Illuminate\Support\Facades\Artisan;
use Nwidart\Modules\Module;

final class GenerateModuleScaffolding
{
    public function handle(string $event, array $payload): void
    {
        $module = $payload[0] ?? null;

        if (! $module instanceof Module) {
            return;
        }

        if ($module->get('providers') === []) {
            return;
        }

        if (filled(data_get($module->get('filament'), 'plugin'))) {
            Artisan::call(ModuleMakeFilamentPluginCommand::class, [
                'module' => $module->getName(),
                '--no-interaction' => true,
            ]);
        }
    }
}
