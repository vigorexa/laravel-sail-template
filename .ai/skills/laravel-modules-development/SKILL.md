---
name: laravel-modules-development
description: "Build and maintain modular Laravel applications with nwidart/laravel-modules v13. Activates when creating, wiring, enabling, disabling or deleting a module; when working with any file under `app-modules/`; when editing `module.json`, `modules_statuses.json`, `config/modules.php`, a module `composer.json`, a `{Module}ServiceProvider`, or a module `RouteServiceProvider`; when running any `module:*` artisan command (`module:make`, `module:make-model`, `module:migrate`, `module:seed`, `module:list`, …); when using the `Nwidart\\Modules\\Facades\\Module` facade, the `Nwidart\\Modules\\Support\\ModuleServiceProvider` base class, the `PathNamespace` trait, or the `module()`, `module_path()` and `module_vite()` helpers; when registering module migrations, seeders, factories, views, translations, config or tests; when a class in the `Modules\\` namespace cannot be autoloaded; and whenever the user mentions modules, modular architecture, app-modules, or laravel-modules. Use this skill for any work that adds or changes code inside a module, because this project's module layout deviates from the package defaults."
---

# Laravel Modules Development

## When to use this skill

Use this skill whenever you add or change code inside `app-modules/`, or create a new module. **This
project's setup deviates from the package defaults in three ways that will break your work if you
follow the upstream docs literally** — read [Deviations from package defaults](#deviations-from-package-defaults)
before generating anything.

Installed version: **`nwidart/laravel-modules` v13.0.0** (Laravel 12, PHP 8.4). Verify with
`composer show nwidart/laravel-modules`. Docs: <https://laravelmodules.com/docs/13>.

## Deviations from package defaults

| Concern | Package default | **This project** |
|---|---|---|
| Module directory | `Modules/` | **`app-modules/`** (`config/modules.php` → `paths.modules`) |
| Autoloading | `wikimedia/composer-merge-plugin` reading `Modules/*/composer.json` | **`app-modules/*/composer.json`** merged via the same plugin (see root `composer.json` → `extra.merge-plugin`) |
| Generated folders | most enabled | **most `generate` flags are `false`** (see `paths.generator`) |

After creating a module, run `composer dump-autoload` so the merge plugin picks up its
`composer.json`. See [Wiring checklist](#wiring-checklist).

## Current module layout

```
app-modules/AccessControl/
├── app/
│   ├── Console/                  artisan commands (registered via $commands)
│   ├── Filament/                 Filament plugin + resources
│   ├── Http/Controllers/
│   ├── Models/
│   ├── Notifications/
│   ├── Policies/
│   ├── Providers/
│   │   ├── AccessControlServiceProvider.php    main provider (in module.json)
│   │   ├── AuthServiceProvider.php             registered via $providers
│   │   └── RouteServiceProvider.php            registered via $providers
│   └── Services/
├── config/config.php
├── database/{factories,migrations,seeders}/
├── routes/api.php
├── tests/Feature/
├── composer.json                 PSR-4 autoload map (merged by composer-merge-plugin)
└── module.json                   name, alias, providers
```

## Wiring checklist

Every step is required. Skipping any one produces a module that silently does nothing.

### 1. Create the module

```bash
./vendor/bin/sail artisan module:make AccessControl --no-interaction
```

Names are StudlyCase. Add `--api` for an API module (no views or web routes), `--plain` for a bare
skeleton, `--disabled` to leave it inactive. Multiple names are accepted in one call.

### 2. Regenerate the autoloader

`module:make` scaffolds `app-modules/{Module}/composer.json` with the correct PSR-4 entries. The root
`composer.json` merges every module file via `wikimedia/composer-merge-plugin`:

```json
"extra": {
    "merge-plugin": {
        "include": ["app-modules/*/composer.json"]
    }
}
```

Run after creating or renaming a module:

```bash
./vendor/bin/sail composer dump-autoload
```

Each module's `composer.json` defines three autoload roots — `app/`, `database/factories/`,
`database/seeders/` — plus `tests/` under `autoload-dev`. Paths are relative to the module directory.
Do **not** duplicate these entries in the root `composer.json`.

### 3. Register the main provider in `module.json`

```json
"providers": [
    "Modules\\AccessControl\\Providers\\AccessControlServiceProvider"
]
```

`nwidart/laravel-modules` loads this array during module bootstrap. Do **not** register module
providers in `bootstrap/providers.php` or in `composer.json` (`extra.laravel.providers` is unused
in this project — merge-plugin handles autoload only).

### 4. Filament plugin via `module.json`

Custom stubs in `stubs/nwidart-stubs/` add a `filament.plugin` entry to `module.json` and generate
`{Module}FilamentPlugin` on `module:make`. `AdminPanelProvider` loads plugins via
`ModuleFilamentPlugins::discover()`.

```json
"filament": {
    "plugin": "Modules\\AccessControl\\Filament\\AccessControlFilamentPlugin"
}
```

Manual command for existing modules: `php artisan module:make-filament-plugin {Module}`.

### 4a. Frontend assets need no wiring

`module:make` scaffolds `resources/assets/js/app.js` and `resources/assets/css/app.css`. The root
`vite.config.js` discovers them automatically, so there is no per-module `vite.config.js` or
`package.json` and nothing to register. Reference them in Blade by their manifest key:

```blade
@vite('app-modules/Blog/resources/assets/js/app.js')
```

Do **not** use `module_vite()` here — it expects a per-module build directory, which this setup does
not produce.

### 5. Module tests in `phpunit.xml`

Module test directories are included via globs — no per-module entry needed:

```xml
<testsuite name="Feature">
    <directory suffix="Test.php">tests/Feature</directory>
    <directory suffix="Test.php">app-modules/*/tests/Feature</directory>
</testsuite>
```

There is a helper for the coverage side:

```bash
./vendor/bin/sail artisan module:update-phpunit-coverage
```

### 6. Verify

```bash
./vendor/bin/sail artisan module:list
./vendor/bin/sail artisan route:list --path=api
./vendor/bin/sail artisan migrate --pretend
```

## Service providers

This project extends the package's `ModuleServiceProvider` base class, which handles config merging,
views, translations, migrations and command registration for you. Keep provider classes this thin:

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

`$name` and `$nameLower` are **mandatory** — the base constructor throws `LogicException` without both.
Everything else is opt-in via `$providers` and `$commands`. Do not hand-write `registerConfig()` /
`registerViews()` / `loadMigrationsFrom()`; the base class already does it. Details and the exact base
class behaviour: [references/service-providers.md](references/service-providers.md).

Route registration lives in a separate provider:

```php
class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'AccessControl';

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

## `module.json`

```json
{
    "name": "AccessControl",
    "alias": "access-control",
    "description": "",
    "keywords": [],
    "priority": 0,
    "providers": ["Modules\\AccessControl\\Providers\\AccessControlServiceProvider"],
    "files": []
}
```

- `alias` drives the view (`access-control::index`), config (`config('access-control.key')`) and
  translation namespaces. Keep it kebab-case and equal to `$nameLower`.
- `priority` controls boot and seed order; lower boots first. Use it when one module depends on another.
- `files` are extra PHP files loaded on boot, e.g. `["helpers.php"]`.

## Custom namespaces

Registered automatically from the alias:

```php
view('access-control::index');
view('access-control::partials.sidebar');
config('access-control.some_key');
Lang::get('access-control::messages.greeting');
```

Note that `config/config.php` is merged under the **alias**, not the file name.

## Enable / disable

State lives in `modules_statuses.json` at the project root. **Commit it** so environments agree.

```bash
./vendor/bin/sail artisan module:enable AccessControl
./vendor/bin/sail artisan module:disable AccessControl
./vendor/bin/sail artisan module:list
```

Programmatically:

```php
use Nwidart\Modules\Facades\Module;

Module::enable('AccessControl');
Module::disable('AccessControl');
```

A disabled module's providers are not registered, so its routes, migrations and commands disappear.

## Generators write where `config/modules.php` says

`paths.generator` maps each generator to a path **and** a `generate` flag. In this project most flags
are `false`, meaning `module:make` will not scaffold those folders — but the generator commands still
work and will create the directory on demand.

Currently `generate: true`: `provider`, `route-provider`, `controller`, `config`, `factory`,
`migration`, `seeder`, `assets`, `views`, `routes`, `test-feature`, `test-unit`.

Currently `generate: false` (created on demand): `actions`, `casts`, `channels`, `class`, `command`,
`component-class`, `emails`, `event`, `enums`, `exceptions`, `jobs`, `helpers`, `interfaces`,
`listener`, `model`, `notifications`, `observer`, `policies`, `repository`, `resource`, `rules`,
`services`, `scopes`, `traits`, `filter`, `request`, `lang`, `component-view`.

Note that `paths.migration` is `base_path('database/migrations')` — that is the **publish** target for
`module:publish-migration`, not where module migrations live. Module migrations stay in
`app-modules/{Module}/database/migrations` and are auto-discovered because `auto-discover.migrations`
is `true`.

Full config reference: [references/config-and-structure.md](references/config-and-structure.md).

## Frequently used commands

```bash
# scaffolding (module name is the LAST argument on make-* commands)
./vendor/bin/sail artisan module:make-model UserModel AccessControl --no-interaction
./vendor/bin/sail artisan module:make-migration create_users_table AccessControl
./vendor/bin/sail artisan module:make-seed UserSeeder AccessControl
./vendor/bin/sail artisan module:make-factory UserModel AccessControl
./vendor/bin/sail artisan module:make-test UserModelTest AccessControl --feature
./vendor/bin/sail artisan module:make-class ExampleService AccessControl --type=service

# database
./vendor/bin/sail artisan module:migrate AccessControl
./vendor/bin/sail artisan module:migrate --all
./vendor/bin/sail artisan module:migrate-status
./vendor/bin/sail artisan module:seed AccessControl
./vendor/bin/sail artisan module:seed --all

# inspection
./vendor/bin/sail artisan module:list
./vendor/bin/sail artisan module:list-commands AccessControl
./vendor/bin/sail artisan module:model-show UserModel
```

**Argument order matters:** on `module:make-*` commands the module name is the *second* (last)
argument — `module:make-model {model} {module}`. Getting this backwards creates a bizarrely named class
in the wrong module.

Complete command reference with every flag: [references/commands.md](references/commands.md).

## Migrations, seeders, factories

- **Migrations** are auto-discovered from each enabled module (`auto-discover.migrations => true`), so
  plain `artisan migrate` runs them. `module:migrate` targets a single module.
- **Seeders** are autoloaded via each module's `composer.json` (merged automatically). This project's full seed step
  is `artisan db:seed && artisan module:seed --all`.
- **Factories** are autoloaded the same way; the model must still point at its factory when the naming
  convention does not match (e.g. `UserModel` → `UserFactory`).

Details: [references/testing-and-database.md](references/testing-and-database.md).

## Common pitfalls

- **`Class "Modules\Foo\..." not found`.** `composer dump-autoload` not run after creating a module, or
  the module's `composer.json` is missing / has wrong PSR-4 paths. Check that root `composer.json`
  includes `extra.merge-plugin.include: ["app-modules/*/composer.json"]`.
- **Module tests never run.** Check that `phpunit.xml` globs cover `app-modules/*/tests/{Feature,Unit}`.
- **Provider not registered.** Add it to `module.json` (`providers` array).
- **`$name`/`$nameLower` missing** → `LogicException` from `ModuleServiceProvider::__construct()`.
- **Wrong argument order on `module:make-*`.** Module name comes last.
- **Following upstream docs for `Modules/` paths.** This project uses `app-modules/`; always resolve
  paths with `module_path()` rather than hard-coding.
- **Factories or seeders in the wrong namespace.** They need their own PSR-4 roots, separate from the
  module's `app/`.
- **Expecting hard isolation.** The package does not enforce boundaries; modules can freely use each
  other's classes. Discipline is on you.
- **`config('access-control.…')` returning null.** Config is merged under the module **alias**, not the
  config file name.

## Reference files

| File | Contents |
|---|---|
| [references/commands.md](references/commands.md) | All 60+ `module:*` commands with arguments and flags |
| [references/config-and-structure.md](references/config-and-structure.md) | Full `config/modules.php` reference, generator paths, stubs, activators |
| [references/service-providers.md](references/service-providers.md) | `ModuleServiceProvider` internals, `RouteServiceProvider`, schedules, `PathNamespace` |
| [references/api-reference.md](references/api-reference.md) | `Module` facade, `Module` instance methods, `FileRepository`, helper functions |
| [references/testing-and-database.md](references/testing-and-database.md) | PHPUnit wiring, migrations, seeders, factories, publishing |
