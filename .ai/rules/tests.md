---
paths:
  - 'tests/**'
  - 'app-modules/**/tests/**'
---

# Tests

## PHPUnit test methods use camelCase
PHPUnit test methods use camelCase names (e.g. `testTheApplicationReturnsASuccessfulResponse`), not snake_case. Applies to `tests/` and `app-modules/*/tests/`. The shared Pint config enforces `php_unit_method_casing`; rename Laravel/Boost-generated `test_the_*` methods before committing.
