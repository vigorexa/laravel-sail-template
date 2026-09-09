<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Nwidart\Modules\Commands\Make\GeneratorCommand;
use Nwidart\Modules\Module;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModuleMakeFilamentPluginCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    protected $name = 'module:make-filament-plugin';

    protected $description = 'Create a Filament panel plugin class for the specified module.';

    protected $argumentName = 'module';

    protected function getArguments(): array
    {
        return [
            ['module', InputArgument::REQUIRED, 'The module name.'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Overwrite the plugin class if it already exists.'],
        ];
    }

    protected function getTemplateContents(): string
    {
        /** @var Module $module */
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return (new Stub('/filament/plugin.stub', [
            'STUDLY_NAME' => $module->getStudlyName(),
            'MODULE_NAMESPACE' => $this->laravel['modules']->config('namespace'),
        ]))->render();
    }

    protected function getDestinationFilePath(): string
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        return $path . 'app/Filament/' . $this->getFilamentPluginClassName() . '.php';
    }

    private function getFilamentPluginClassName(): string
    {
        /** @var Module $module */
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return $module->getStudlyName() . 'FilamentPlugin';
    }
}
