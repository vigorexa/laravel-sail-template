# Testing, migrations, seeders and factories in modules

## Testing

### Wiring the test suite

Module test directories are included via globs in the root `phpunit.xml` — no per-module entry needed:

```xml
<testsuites>
    <testsuite name="Unit">
        <directory suffix="Test.php">tests/Unit</directory>
        <directory suffix="Test.php">app-modules/*/tests/Unit</directory>
    </testsuite>
    <testsuite name="Feature">
        <directory suffix="Test.php">tests/Feature</directory>
        <directory suffix="Test.php">app-modules/*/tests/Feature</directory>
    </testsuite>
</testsuites>
```

For coverage `<source>` paths there is a helper:

```bash
./vendor/bin/sail artisan module:update-phpunit-coverage
```

The root `phpunit.xml` already includes `app-modules` in `<source>`; run the helper after adding modules if coverage paths need refreshing.

### Autoloading tests

Test classes are autoloaded via each module's `composer.json` (`autoload-dev`), merged by
`composer-merge-plugin`. After adding a module, run `composer dump-autoload`.

### Test class shape

Extend the application's base `Tests\TestCase` so module tests share bootstrapping:

```php
declare(strict_types=1);

namespace Modules\AccessControl\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AccessControl\Models\UserModel;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_user(): void
    {
        // …
    }
}
```

This project is **PHPUnit-only**. Write PHPUnit classes; convert any Pest-style test you find.

### Generating tests

```bash
./vendor/bin/sail artisan module:make-test UserModelTest AccessControl --feature
./vendor/bin/sail artisan module:make-test UserModelTest AccessControl
```

Without `--feature` a unit test is created. Remember the module name comes last.

### Running tests

```bash
./vendor/bin/sail artisan test --compact
./vendor/bin/sail artisan test --compact app-modules/AccessControl/tests/Feature
./vendor/bin/sail artisan test --compact app-modules/AccessControl/tests/Feature/UserModelTest.php
./vendor/bin/sail artisan test --compact --filter=test_it_can_create_a_user
```

Test environment settings come from `phpunit.xml` (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`,
`BCRYPT_ROUNDS=4`, array cache/session/mail) and `.env.testing`.

## Migrations

### Location and discovery

Module migrations live in `app-modules/{Module}/database/migrations`. Two mechanisms load them:

1. `ModuleServiceProvider::boot()` calls
   `loadMigrationsFrom(module_path($this->name, config('modules.paths.generator.migration.path')))`.
2. `config('modules.auto-discover.migrations')` is `true`.

So plain `artisan migrate` runs module migrations. Use `module:migrate` to target one module.

```bash
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan module:migrate AccessControl
./vendor/bin/sail artisan module:migrate --all
./vendor/bin/sail artisan module:migrate-status
./vendor/bin/sail artisan module:migrate-rollback AccessControl
./vendor/bin/sail artisan module:migrate --pretend
```

### Ordering across modules

Migrations run in filename order per module, and modules run in `priority` order (from `module.json`,
lower first). When a module's tables reference another module's, either lower that module's `priority`
or use timestamp prefixes that sort correctly.

This project uses deliberately sequenced names so ordering is explicit:

```
0001_01_01_000000_create_users_table.php
0001_01_01_000001_create_password_reset_tokens_table.php
0001_01_03_000000_create_permission_tables.php
```

Follow this scheme when adding migrations to an existing module.

### Generating

```bash
./vendor/bin/sail artisan module:make-migration create_posts_table AccessControl
./vendor/bin/sail artisan module:make-migration create_posts_table AccessControl --fields="title:string,body:text"
./vendor/bin/sail artisan module:make-migration adjust_posts AccessControl --plain
```

When altering a column, restate **all** its previous attributes or they are dropped.

### Publishing to the app

```bash
./vendor/bin/sail artisan module:publish-migration AccessControl
```

Copies migrations into `config('modules.paths.migration')` (`database/migrations`). Only useful when you
want the app to own them; normally leave them in the module.

## Seeders

### Namespace requirement

Seeders are autoloaded from the module's `composer.json`:

```json
"autoload": {
    "psr-4": {
        "Modules\\AccessControl\\Database\\Seeders\\": "database/seeders/"
    }
}
```

This is merged automatically — no root `composer.json` entry needed.

### Structure

A master seeder calls the rest:

```php
declare(strict_types=1);

namespace Modules\AccessControl\Database\Seeders;

use Illuminate\Database\Seeder;

class AccessControlDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SuperuserSeeder::class,
            UserForEachRoleSeeder::class,
        ]);
    }
}
```

Mark the master seeder at generation time:

```bash
./vendor/bin/sail artisan module:make-seed AccessControlDatabaseSeeder AccessControl --master
./vendor/bin/sail artisan module:make-seed RoleSeeder AccessControl
```

### Running

```bash
./vendor/bin/sail artisan module:seed AccessControl
./vendor/bin/sail artisan module:seed --all
./vendor/bin/sail artisan module:seed AccessControl --class=RoleSeeder
```

This project's documented full seed step:

```bash
./vendor/bin/sail artisan db:seed && ./vendor/bin/sail artisan module:seed --all
```

`module:seed --all` respects module `priority`, and `-d desc` reverses the order.

## Factories

### Namespace requirement

Factories are autoloaded from the module's `composer.json`:

```json
"autoload": {
    "psr-4": {
        "Modules\\AccessControl\\Database\\Factories\\": "database/factories/"
    }
}
```

Laravel's factory resolver expects a `Database\Factories` namespace segment — that is why this needs its
own PSR-4 root rather than falling under the module's `app/`.

### Definition

```php
declare(strict_types=1);

namespace Modules\AccessControl\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\AccessControl\Models\UserModel;

class UserFactory extends Factory
{
    protected $model = UserModel::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => ['email_verified_at' => null]);
    }
}
```

Note the factory class name does not have to mirror the model name (`UserFactory` for `UserModel`), which
is why the explicit `$model` property matters.

### Linking a model to its factory

When the naming convention does not resolve — as with the `Model` suffix used here — declare it
explicitly:

```php
namespace Modules\AccessControl\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AccessControl\Database\Factories\UserFactory;

class UserModel extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    protected static string $factory = UserFactory::class;
}
```

Or override `newFactory()`:

```php
protected static function newFactory(): UserFactory
{
    return UserFactory::new();
}
```

Symptom of a missing link: `Class "Database\Factories\Modules\AccessControl\Models\UserModelFactory"
not found`.

### Generating

```bash
./vendor/bin/sail artisan module:make-factory UserModel AccessControl
```

The argument is the **model** name.

Prefer factory states over manually assembling attributes in tests, and check for existing states before
adding new ones.

## Config, views, translations, assets

Handled by `ModuleServiceProvider` — see [service-providers.md](service-providers.md). Publish targets:

```bash
./vendor/bin/sail artisan module:publish-config AccessControl
./vendor/bin/sail artisan module:publish-translation AccessControl
./vendor/bin/sail artisan module:publish AccessControl        # assets → public/modules
```

Publishing config copies `config/config.php` to `config/access-control.php`. The module's own config is
merged first and the published file overrides it via `array_replace_recursive`.

## Model pruning

```bash
./vendor/bin/sail artisan module:prune
```

Runs Laravel's prunable-model logic scoped to modules.

## Inspecting models

```bash
./vendor/bin/sail artisan module:model-show UserModel
```

Module-aware equivalent of `artisan model:show`.
