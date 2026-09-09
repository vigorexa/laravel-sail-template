# Module service providers

## `Nwidart\Modules\Support\ModuleServiceProvider`

Extend this base class. It removes essentially all boilerplate from module providers. Reading its
actual behaviour prevents duplicated or conflicting registration.

```php
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

    protected array $commands = [
        // ExampleCommand::class,
    ];
}
```

### Required properties

| Property | Type | Notes |
|---|---|---|
| `$name` | `string` | StudlyCase module name. **Required.** |
| `$nameLower` | `string` | kebab/lowercase alias, matching `module.json`'s `alias`. **Required.** |
| `$providers` | `string[]` | Registered in `register()` |
| `$commands` | `string[]` | Registered in `boot()` |

The constructor throws if either name is missing:

```php
if (! isset($this->name, $this->nameLower)) {
    throw new \LogicException('Module service provider must define both $name and $nameLower properties.');
}
```

### What `boot()` does for you

```php
public function boot(): void
{
    $this->registerCommands();
    $this->registerCommandSchedules();
    $this->registerTranslations();
    $this->registerConfig();
    $this->registerViews();

    $generatorMigrationPath = config('modules.paths.generator.migration.path') ?? 'database/migrations';
    $this->loadMigrationsFrom(module_path($this->name, $generatorMigrationPath));
}
```

So migrations, config, views, translations and commands are **already registered**. Do not re-register
them — you will get duplicate publish groups and duplicated migration paths.

### What `register()` does

```php
public function register(): void
{
    foreach ($this->providers as $provider) {
        $this->app->register($provider);
    }
}
```

If you override `register()`, call `parent::register()` or your sub-providers will never load.

### Config registration details

`registerConfig()` walks `{module}/config/` recursively and, for each PHP file:

- merges it into the config repository under a key derived from `$nameLower` plus the relative path,
  with adjacent duplicate segments collapsed;
- for `config.php` specifically, the key is just `$nameLower` and the publish target is
  `config_path("{$nameLower}.php")`;
- registers a `config` publish group.

So `app-modules/AccessControl/config/config.php` is read as **`config('access-control.*')`** — the module
alias, not the file name. A nested `config/services.php` becomes `config('access-control.services.*')`.

Merging uses `array_replace_recursive` and is **skipped entirely when the config is cached**. After
changing module config in a deployed environment, run `artisan config:clear`.

### View registration details

`registerViews()` registers, in order of precedence:

1. published overrides in `resource_path('views/modules/{alias}')` (and any `view.paths` entry
   containing `modules/{alias}`),
2. the module's own `resources/views`.

It also registers a Blade component namespace:

```php
Blade::componentNamespace('Modules\\AccessControl\\View\\Components', 'access-control');
```

So `<x-access-control::alert />` resolves to `Modules\AccessControl\View\Components\Alert`.

### Translation registration details

`registerTranslations()` prefers `resource_path('lang/modules/{alias}')` if it exists, otherwise falls
back to the module's own lang path from `config('modules.paths.generator.lang.path')`. Both PHP and JSON
translations are loaded.

### Scheduled tasks

Define `configureSchedules()` on the provider and the base class wires it up on `booted`:

```php
use Illuminate\Console\Scheduling\Schedule;

protected function configureSchedules(Schedule $schedule): void
{
    $schedule->command('access-control:purge-one-time-passwords')->hourly();
}
```

The base class only calls it when the method exists, so there is no need to declare an empty one.

## `RouteServiceProvider`

Kept separate and registered via `$providers`.

```php
declare(strict_types=1);

namespace Modules\AccessControl\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'AccessControl';

    public function boot(): void
    {
        parent::boot();
    }

    public function map(): void
    {
        $this->mapApiRoutes();
    }

    protected function mapApiRoutes(): void
    {
        Route::middleware('api')
            ->prefix('/api/access-control')
            ->name('api.access-control.')
            ->group(module_path($this->name, '/routes/api.php'));
    }
}
```

Points that matter:

- Always resolve route files with `module_path()`. Hard-coded `base_path('Modules/...')` is wrong here —
  modules live in `app-modules/`.
- The `prefix()` and `name()` values must line up with whatever the module's routes assume.
- Add `mapWebRoutes()` the same way if the module serves web routes:

```php
protected function mapWebRoutes(): void
{
    Route::middleware('web')
        ->group(module_path($this->name, '/routes/web.php'));
}
```

## `AuthServiceProvider`

Policies are registered per module:

```php
namespace Modules\AccessControl\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\AccessControl\Models\{PermissionModel, RoleModel, UserModel};
use Modules\AccessControl\Policies\{PermissionPolicy, RolePolicy, UserPolicy};

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        UserModel::class => UserPolicy::class,
        RoleModel::class => RolePolicy::class,
        PermissionModel::class => PermissionPolicy::class,
    ];
}
```

## `PathNamespace` trait

`Nwidart\Modules\Traits\PathNamespace` — helpers for building namespaces and paths from config, useful
in generators and dynamic registration.

| Method | Returns |
|---|---|
| `studly_path(string $path, $ds = '/')` | StudlyCase path |
| `studly_namespace(string $namespace, $ds = '\\')` | StudlyCase namespace |
| `path_namespace(string $path)` | Namespace derived from a path |
| `module_namespace(string $module, ?string $path = null)` | Full module namespace, optionally suffixed |
| `clean_path(string $path, $ds = '/')` | Normalised path |
| `app_path(?string $path = null)` | Module app path |

```php
$this->module_namespace('AccessControl', 'Http/Controllers');
// → Modules\AccessControl\Http\Controllers
```

`module_namespace()` builds on `config('modules.namespace')`, falling back to a namespace derived from
`config('modules.paths.modules')`.

## Registering the provider with the application

Register the Filament plugin in `module.json`:

```json
"filament": {
    "plugin": "Modules\\AccessControl\\Filament\\AccessControlFilamentPlugin"
}
```

`ModuleFilamentPlugins::discover()` reads this key from all enabled modules and registers plugins in
`AdminPanelProvider`. New modules get the plugin class from `stubs/nwidart-stubs/filament/plugin.stub`
automatically on `module:make`.

## Filament integration

Modules expose admin UI through a Filament plugin class:

```php
// app-modules/AccessControl/app/Filament/AccessControlFilamentPlugin.php
namespace Modules\AccessControl\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;

class AccessControlFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'access-control';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: module_path('AccessControl', 'app/Filament/Resources'),
            for: 'Modules\\AccessControl\\Filament\\Resources',
        );
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
```

Registered from the app panel provider:

```php
// app/Providers/AdminPanelProvider.php
use Illuminate\Support\Facades\App;
use Modules\AccessControl\Filament\AccessControlFilamentPlugin;

return $panel->plugins([
    App::make(AccessControlFilamentPlugin::class),
]);
```

Discovery paths must use `module_path()` so they keep working if `paths.modules` changes.
