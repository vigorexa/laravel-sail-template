# Programmatic API

## Helper functions

Defined in `vendor/nwidart/laravel-modules/src/helpers.php`.

### `module_path()`

```php
function module_path(string $name, string $path = ''): string
```

Absolute path to a module, optionally with a sub-path appended. **Always use this instead of
hard-coding `base_path('Modules/...')`** — this project stores modules in `app-modules/`.

```php
module_path('AccessControl');                          // …/app-modules/AccessControl
module_path('AccessControl', 'routes/api.php');
module_path('AccessControl', 'app/Filament/Resources');
```

Throws if the module does not exist (it calls `find()` and dereferences the result).

### `module()`

```php
function module(string $name, bool $instance = false): bool|Module
```

Returns the module's **enabled status** by default, or the `Module` instance when `$instance` is `true`.
Returns `false` when the module does not exist, so it is safe for feature checks:

```php
if (module('AccessControl')) {
    // module exists and is enabled
}

$module = module('AccessControl', true);   // Module|false
```

### `module_vite()`

```php
function module_vite(string $module, string $asset, ?string $hotFilePath = null): Vite
```

Vite instance scoped to a module's build directory, defaulting the hot file to
`storage_path('vite.hot')`:

```blade
{{ module_vite('AccessControl', 'resources/assets/js/app.js') }}
```

## `Module` facade

`Nwidart\Modules\Facades\Module`, backed by `Nwidart\Modules\FileRepository`.

```php
use Nwidart\Modules\Facades\Module;
```

### Finding modules

| Method | Returns |
|---|---|
| `all()` | All modules |
| `allEnabled()` | Enabled modules |
| `allDisabled()` | Disabled modules |
| `getByStatus(bool $status)` | Modules with the given status |
| `getOrdered(string $direction = 'asc')` | Modules ordered by `priority` |
| `find(string $name)` | `Module|null` |
| `findOrFail(string $name)` | `Module`, throws `ModuleNotFoundException` |
| `has(string $name)` | `bool` |
| `count()` | `int` |
| `toCollection()` / `collections()` | Collection of modules |
| `getUsedNow()` | The module set by `module:use` |

### Status

```php
Module::isEnabled('AccessControl');
Module::isDisabled('AccessControl');
Module::enable('AccessControl');
Module::disable('AccessControl');
```

### Paths and assets

| Method | Returns |
|---|---|
| `getPath()` | The modules root (`app-modules`) |
| `getPaths()` | All registered module locations |
| `getScanPaths()` | Paths scanned for modules |
| `getModulePath(string $name)` | Path to one module |
| `getAssetsPath()` | Public assets path (`public/modules`) |
| `asset(string $asset)` | URL to a module asset |
| `assetPath(string $module)` | Filesystem asset path |
| `getStubPath()` / `setStubPath()` | Stub path |
| `getFiles()` | Module files |
| `getUsedStoragePath()` | Where `module:use` state is stored |

### Lifecycle and management

`register()`, `boot()`, `scan()`, `resetModules()`, `addLocation()`, `delete()`, `install()`,
`update()`, `setUsed()`, `forgetUsed()`, `config()`.

## `Module` instance

`Nwidart\Modules\Module`.

```php
$module = Module::findOrFail('AccessControl');
```

### Names

```php
$module->getName();        // "AccessControl"
$module->getLowerName();   // "accesscontrol"
$module->getStudlyName();  // "AccessControl"
$module->getKebabName();   // "access-control"
$module->getSnakeName();   // "access_control"
(string) $module;          // "AccessControl"
```

Note that `getLowerName()` simply lowercases and is **not** the same as the `alias` for multi-word
names — `AccessControl` gives `accesscontrol`, whereas the alias is `access-control`. For the alias use
`$module->get('alias')` or `getKebabName()`.

### Metadata

```php
$module->getDescription();          // module.json "description"
$module->getPriority();             // module.json "priority" (int)
$module->get('alias');              // any module.json key
$module->get('missing', 'default');
$module->getComposerAttr('name');   // any composer.json key
```

### Paths

```php
$module->getPath();                        // module root
$module->getAppPath();                     // module app/ directory
$module->getExtraPath('routes');           // module/routes
$module->getExtraPath('resources/views');
$module->getCachedServicesPath();
$module->setPath($path);
```

`module_path('AccessControl', 'routes')` is equivalent to `$module->getExtraPath('routes')`.

### Status

```php
$module->isEnabled();
$module->isDisabled();
$module->isStatus(true);
$module->enable();
$module->disable();
$module->setActive(true);
```

### Lifecycle

```php
$module->register();          // register providers and aliases
$module->boot();              // boot, fires the 'boot' event
$module->registerProviders();
$module->registerAliases();
$module->fireEvent('custom-event');
$module->delete();
```

### JSON access

```php
$module->json();                  // parsed module.json
$module->json('composer.json');   // parsed composer.json
```

## Practical snippets

### Iterate enabled modules

```blade
@foreach (Module::allEnabled() as $module)
    <div>
        <strong>{{ $module->getName() }}</strong>
        <span>{{ $module->getDescription() }}</span>
        <code>Priority: {{ $module->getPriority() }}</code>
    </div>
@endforeach
```

### Guard code on a module being present

```php
if (module('AccessControl')) {
    // safe: false when missing or disabled
}
```

Prefer this over `Module::find(...)->isEnabled()`, which throws on a missing module.

### Collect all module paths

```php
$paths = collect(Module::all())
    ->map(fn (Module $m) => $m->getPath())
    ->values()
    ->all();
```

### Register something for every enabled module

```php
foreach (Module::allEnabled() as $module) {
    $this->loadViewsFrom(
        $module->getExtraPath('resources/views'),
        $module->getKebabName(),
    );
}
```

## Exceptions

`Nwidart\Modules\Exceptions\ModuleNotFoundException` — thrown by `findOrFail()` and, indirectly, by
`module_path()` for an unknown module.

## Contracts

`Nwidart\Modules\Contracts\*` holds the repository and activator interfaces. Implement
`ActivatorInterface` for a non-file activation strategy (e.g. database-backed) and register it under a
new key in `config('modules.activators')`, then point `config('modules.activator')` at it.
