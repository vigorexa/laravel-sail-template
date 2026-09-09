# `config/modules.php` and module structure

## Top-level keys

| Key | This project | Purpose |
|---|---|---|
| `namespace` | `'Modules'` | Root namespace for module classes |
| `stubs` | `enabled => false` | Custom stub configuration |
| `paths` | see below | Where modules live and where generators write |
| `auto-discover` | `migrations => true`, `translations => false` | Automatic registration |
| `commands` | `ConsoleServiceProvider::defaultCommands()` | Which `module:*` commands are available |
| `scan` | `enabled => false` | Scan `vendor/*/*` for modules |
| `register` | `translations => true`, `files => 'register'` | Namespace registration behaviour |
| `activators` / `activator` | `file` → `modules_statuses.json` | How enable/disable state is stored |

## `paths`

```php
'paths' => [
    'modules'    => base_path('app-modules'),          // NOT the default base_path('Modules')
    'assets'     => public_path('modules'),
    'migration'  => base_path('database/migrations'),  // publish target, not module storage
    'app_folder' => 'app/',
    'generator'  => [ /* … */ ],
],
```

`paths.migration` is where `module:publish-migration` copies files. Module migrations themselves live in
`app-modules/{Module}/database/migrations` and are loaded by the service provider.

## `paths.generator`

Each entry has a `path` and a `generate` flag. `generate => false` means `module:make` will not create
the folder up front; the corresponding `module:make-*` command still works and creates it on demand.

### `generate => true` in this project

| Key | Path |
|---|---|
| `provider` | `app/Providers` |
| `route-provider` | `app/Providers` |
| `controller` | `app/Http/Controllers` |
| `config` | `config` |
| `factory` | `database/factories` |
| `migration` | `database/migrations` |
| `seeder` | `database/seeders` |
| `assets` | `resources/assets` |
| `views` | `resources/views` |
| `routes` | `routes` |
| `test-feature` | `tests/Feature` |
| `test-unit` | `tests/Unit` |

### `generate => false` in this project

| Key | Path |
|---|---|
| `actions` | `app/Actions` |
| `casts` | `app/Casts` |
| `channels` | `app/Broadcasting` |
| `class` | `app/Classes` |
| `command` | `app/Console` |
| `component-class` | `app/View/Components` |
| `emails` | `app/Emails` |
| `event` | `app/Events` |
| `enums` | `app/Enums` |
| `exceptions` | `app/Exceptions` |
| `jobs` | `app/Jobs` |
| `helpers` | `app/Helpers` |
| `interfaces` | `app/Interfaces` |
| `listener` | `app/Listeners` |
| `model` | `app/Models` |
| `notifications` | `app/Notifications` |
| `observer` | `app/Observers` |
| `policies` | `app/Policies` |
| `repository` | `app/Repositories` |
| `resource` | `app/Transformers` |
| `rules` | `app/Rules` |
| `services` | `app/Services` |
| `scopes` | `app/Models/Scopes` |
| `traits` | `app/Traits` |
| `filter` | `app/Http/Middleware` |
| `request` | `app/Http/Requests` |
| `lang` | `resources/lang` |
| `component-view` | `resources/views/components` |

Two entries worth noting because they surprise people: `resource` maps to `app/Transformers` (not
`app/Http/Resources`), and `filter` maps to `app/Http/Middleware`.

To change where a generator writes, edit the `path`. To have `module:make` scaffold a folder, flip
`generate` to `true`.

## `auto-discover`

```php
'auto-discover' => [
    'migrations'   => true,   // module migrations run under plain `artisan migrate`
    'translations' => false,  // lang files are NOT auto-registered
],
```

With `translations => false`, translation registration relies on `ModuleServiceProvider::registerTranslations()`,
which the base class calls on boot.

## `register`

```php
'register' => [
    'translations' => true,
    'files' => 'register',   // module.json "files" are loaded in register(), not boot()
],
```

## `activators`

```php
'activators' => [
    'file' => [
        'class' => FileActivator::class,
        'statuses-file' => base_path('modules_statuses.json'),
    ],
],
'activator' => 'file',
```

`modules_statuses.json`:

```json
{
    "AccessControl": true
}
```

Commit this file. `FileActivator` caches status, so if you edit the file by hand rather than using
`module:enable` / `module:disable`, clear caches (`artisan optimize:clear`).

## `composer`

```php
'composer' => [
    'vendor' => env('MODULE_VENDOR', 'example'),
    'author' => [
        'name'  => env('MODULE_AUTHOR_NAME', 'developer'),
        'email' => env('MODULE_AUTHOR_EMAIL', 'developer@example.com'),
    ],
    'composer-output' => false,
],
```

Override per-module with `--author-name`, `--author-email`, `--author-vendor` on `module:make`.

## `scan`

```php
'scan' => [
    'enabled' => false,
    'paths'   => [base_path('vendor/*/*')],
],
```

Enable to treat installed Composer packages as modules.

## `commands`

```php
'commands' => ConsoleServiceProvider::defaultCommands()
    ->merge([
        // add your own module-aware commands here
    ])->toArray(),
```

## Stubs

`stubs.enabled` is `false` in this project, so the package's built-in stubs are used from
`vendor/nwidart/laravel-modules/src/Commands/stubs`.

To customise, publish them and set `enabled => true` plus a `path`:

```bash
./vendor/bin/sail artisan vendor:publish \
  --provider="Nwidart\Modules\LaravelModulesServiceProvider" --tag="stubs"
```

### `stubs.files`

Maps a stub to its destination inside a new module. This project generates only:

```php
'files' => [
    'routes/web'      => 'routes/web.php',
    'routes/api'      => 'routes/api.php',
    'scaffold/config' => 'config/config.php',
    'composer'        => 'composer.json',
    'assets/js/app'   => 'resources/assets/js/app.js',
    'assets/css/app'  => 'resources/assets/css/app.css',
],
```

View stubs are commented out. Per-module `vite.config.js` and `package.json` are deliberately **not**
generated: the root `vite.config.js` scans `app-modules/*/resources/assets/{css,js}/app.*` and adds
every existing entrypoint to its `input`, so `module:make` alone produces a buildable module. Include
the asset in Blade with the manifest key, not `module_vite()`:

```blade
@vite('app-modules/Blog/resources/assets/js/app.js')
```

The Docker `frontend` stage copies `app-modules/*/resources` alongside the root `resources/`, so module
assets end up in the image. That `COPY` uses a glob and fails if no module has a `resources/` directory.

### `stubs.replacements`

Per-stub placeholder lists. Available keys include `LOWER_NAME`, `STUDLY_NAME`, `PLURAL_LOWER_NAME`,
`KEBAB_NAME`, `MODULE_NAMESPACE`, `CONTROLLER_NAMESPACE`, `PROVIDER_NAMESPACE`, `APP_FOLDER_NAME`,
`VENDOR`, `AUTHOR_NAME`, `AUTHOR_EMAIL`.

Custom replacements may be closures receiving the generator:

```php
'composer' => [
    'CUSTOM_KEY' => fn (\Nwidart\Modules\Generators\ModuleGenerator $generator) =>
        $generator->getModule()->getLowerName() . '-module',
    'CUSTOM_KEY2' => fn () => 'custom text',
    'LOWER_NAME',
    'STUDLY_NAME',
],
```

Keys must be UPPERCASE. `stubs.gitkeep` controls whether `.gitkeep` files are added to empty folders.

## Module `composer.json`

```json
{
    "name": "vigorexa/module-access-control",
    "extra": {
        "laravel": { "providers": [], "aliases": {} }
    },
    "autoload": {
        "psr-4": {
            "Modules\\AccessControl\\": "app/",
            "Modules\\AccessControl\\Database\\Factories\\": "database/factories/",
            "Modules\\AccessControl\\Database\\Seeders\\": "database/seeders/"
        }
    },
    "autoload-dev": {
        "psr-4": { "Modules\\AccessControl\\Tests\\": "tests/" }
    }
}
```

**This project merges this file automatically.** The root `composer.json` includes:

```json
"extra": {
    "merge-plugin": {
        "include": ["app-modules/*/composer.json"]
    }
}
```

Composer resolves PSR-4 paths relative to each module directory. After creating or renaming a
module, run `composer dump-autoload`. Do **not** duplicate these entries in the root `composer.json`.

Register service providers in `module.json`, not in `extra.laravel.providers` — nwidart reads
`module.json` at bootstrap; merged `extra` is not consumed by Laravel package discovery.

`config.allow-plugins.wikimedia/composer-merge-plugin` must remain `true` — if set to `false`, module
classes will not autoload.

## Default generated structure

For reference, `module:make Blog` with default upstream config produces:

```
Blog/
├── app/
│   ├── Http/Controllers/BlogController.php
│   ├── Models/
│   └── Providers/{BlogServiceProvider,RouteServiceProvider}.php
├── config/config.php
├── database/{factories,migrations,seeders}/
│   └── seeders/BlogDatabaseSeeder.php
├── resources/{assets/{js,sass},views/{layouts/master.blade.php,index.blade.php}}
├── routes/{api.php,web.php}
├── tests/{Feature,Unit}/
├── composer.json
├── module.json
├── package.json
└── vite.config.js
```

This project's trimmed `stubs.files` and `generate` flags produce considerably less.
