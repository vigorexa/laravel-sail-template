---
paths:
  - 'app-modules/**'
---

# App Modules

## Modular layout under app-modules
Feature code lives in `app-modules/{Module}/` via nwidart/laravel-modules (namespace `Modules\{Module}`). Register routes, Filament plugins, migrations, and seeders in the module's service providers; run module seeders with `artisan module:seed --all`, not the root `DatabaseSeeder`.
