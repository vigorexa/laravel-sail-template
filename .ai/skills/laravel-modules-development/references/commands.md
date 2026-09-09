# `module:*` command reference

Generated from `artisan list module` and `--help` against the installed v13.0.0. Always prefix with
`./vendor/bin/sail`, and pass `--no-interaction` in scripted use.

**Argument order:** on every `module:make-*` command the module name is the **last** argument.

## Module lifecycle

### `module:make`

```
module:make [options] [--] [<name>...]
```

| Option | Purpose |
|---|---|
| `-p, --plain` | Minimal module, no scaffolded resources |
| `--api` | API module: API controller, no views/Blade/web routes |
| `--web` | Default scaffold: web routes, controller, views, layout |
| `--inertia` | Inertia module: Inertia controller + JS pages, no Blade |
| `-d, --disabled` | Do not enable at creation |
| `--force` | Overwrite an existing module |
| `--author-name=` | Author name written to the module `composer.json` |
| `--author-email=` | Author email |
| `--author-vendor=` | Vendor name |

Accepts several names: `module:make Blog Shop Users`. Author defaults come from

### Other lifecycle commands

| Command | Arguments / options | Purpose |
|---|---|---|
| `module:enable` | `[<module>...]`, `-a\|--all` | Enable modules |
| `module:disable` | `[<module>...]`, `-a\|--all` | Disable modules |
| `module:list` | `-o\|--only=`, `-d\|--direction=` (`asc`) | List modules and status |
| `module:delete` | `<module>` | **Permanently** delete the directory |
| `module:setup` | — | Create the modules folder for first use |
| `module:install` | `<name>` (`vendor/name`) | Install a module from a package |
| `module:update` | `[<module>...]` | Update module dependencies |
| `module:composer-update` | — | Update autoload in `composer.json` |
| `module:dump` | `[<module>...]` | Dump-autoload one or all modules |
| `module:use` / `module:unuse` | `<module>` | Set / forget the "current" module so later commands can omit it |
| `module:v6:migrate` | — | Migrate v5 statuses to v6 format |
| `module:list-commands` | `[<module>...]` | List commands provided by modules |
| `module:lang` | `<module>` | Report missing translation keys |
| `module:update-phpunit-coverage` | — | Sync `phpunit.xml` source paths with enabled modules |

## Class generators

### `module:make-model`

```
module:make-model [options] [--] <model> [<module>]
```

| Option | Purpose |
|---|---|
| `-a, --all` | Create every associated file |
| `-c, --controller` | Also create a controller |
| `--fillable=` | Comma-separated fillable attributes |
| `-f, --factory` | Also create a factory |
| `-m, --migration` | Also create a migration |
| `-r, --request` | Also create a form request |
| `-R, --resource` | Also create an API resource |
| `-p, --policy` | Also create a policy |
| `-s, --seed` | Also create a seeder |

### `module:make-controller`

```
module:make-controller [options] [--] <controller> [<module>]
```

`-p|--plain`, `--api` (no create/edit), `-i|--invokable`, `--inertia`.

### `module:make-migration`

```
module:make-migration [options] [--] <name> [<module>]
```

`--fields=` (specify table fields), `--plain` (empty migration).

### `module:make-seed`

```
module:make-seed [options] [--] <name> [<module>]
```

`--master` marks it as the module's root database seeder — the one `module:seed` invokes.

### `module:make-factory`

```
module:make-factory <name> [<module>]
```

`<name>` is the **model** name. No options.

### `module:make-provider`

```
module:make-provider [options] [--] <name> [<module>]
```

`--master` marks it as the module's main service provider.

### `module:route-provider`

```
module:route-provider [options] [--] [<module>]
```

`--force`. Generates the module's `RouteServiceProvider`.

### `module:make-test`

```
module:make-test [options] [--] <name> [<module>]
```

`--feature` creates a feature test (otherwise a unit test).

### `module:make-class`

```
module:make-class [options] [--] <name> <module>
```

| Option | Purpose |
|---|---|
| `-t, --type=` | Class kind, e.g. `class`, `service`, `repository`, `contract` (default `class`) |
| `-s, --suffix` | Create the class **without** the type suffix |
| `-i, --invokable` | Single `__invoke` method |
| `-f, --force` | Overwrite |

Note that `<module>` is **required** here, unlike most other generators.

### `module:make-replacement`

```
module:make-replacement <name>
```

Creates a replacement-key command class for stub placeholders.

### Simple generators

All follow `module:make-x <name> [<module>]` with no notable options:

`module:make-action`, `module:make-cast`, `module:make-channel`, `module:make-command`,
`module:make-component`, `module:make-component-view`, `module:make-enum`, `module:make-event`,
`module:make-event-provider`, `module:make-exception`, `module:make-helper`,
`module:make-inertia-component`, `module:make-inertia-page`, `module:make-interface`,
`module:make-job`, `module:make-listener`, `module:make-mail`, `module:make-middleware`,
`module:make-notification`, `module:make-observer`, `module:make-policy`, `module:make-repository`,
`module:make-request`, `module:make-resource`, `module:make-rule`, `module:make-scope`,
`module:make-service`, `module:make-trait`, `module:make-view`.

Destination paths come from `config('modules.paths.generator.*')`.

## Migrations

### `module:migrate`

```
module:migrate [options] [--] [<module>...]
```

| Option | Purpose |
|---|---|
| `-d, --direction=` | Ordering, `asc` (default) or `desc` |
| `--database=` | Connection |
| `--pretend` | Dump SQL without executing |
| `--force` | Allow in production |
| `--seed` | Re-run seeders afterwards |
| `--subpath=` | Run migrations from a subpath |
| `-a, --all` | All modules |

### Other migration commands

`module:migrate-rollback`, `module:migrate-refresh`, `module:migrate-reset`, `module:migrate-fresh`,
`module:migrate-status` — same shape, mirroring Laravel's equivalents.

Because `auto-discover.migrations` is `true` in this project, plain `artisan migrate` already runs
module migrations. Use `module:migrate` when you need to target one module.

## Seeders

### `module:seed`

```
module:seed [options] [--] [<module>...]
```

| Option | Purpose |
|---|---|
| `-d, --direction=` | Ordering, `asc` (default) or `desc` |
| `--class=` | Root seeder class name |
| `--database=` | Connection |
| `--force` | Allow in production |
| `-a, --all` | All modules |

Ordering respects each module's `priority` from `module.json`.

## Publishing

| Command | Arguments / options |
|---|---|
| `module:publish` | `[<module>...]` — publish assets to `public/modules` |
| `module:publish-config` | `[<module>...]`, `-f\|--force`, `-a\|--all` |
| `module:publish-migration` | `[<module>...]` — copy migrations to `database/migrations` |
| `module:publish-translation` | `[<module>...]` |
| `module:publish-inertia` | Publish the Inertia `app.js` resolver |

## Miscellaneous

| Command | Purpose |
|---|---|
| `module:model-show` | Show Eloquent model info for models inside modules |
| `module:prune` | Prune prunable models, per module |

## Vendor publishing (package-level)

```bash
# config/modules.php
./vendor/bin/sail artisan vendor:publish \
  --provider="Nwidart\Modules\LaravelModulesServiceProvider" --tag="config"

# customisable stubs
./vendor/bin/sail artisan vendor:publish \
  --provider="Nwidart\Modules\LaravelModulesServiceProvider" --tag="stubs"

# Vite module loader helper
./vendor/bin/sail artisan vendor:publish \
  --provider="Nwidart\Modules\LaravelModulesServiceProvider" --tag="vite"
```

## Discovery

```bash
./vendor/bin/sail artisan list module
./vendor/bin/sail artisan module:make-model --help
```
