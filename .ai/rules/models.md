---
paths:
  - 'app-modules/**/app/Models/**'
---

# Models

## Eloquent models use Model suffix and table prefix
Domain Eloquent models live in modules under `app/Models/` and are named `{Entity}Model` (e.g. `UserModel`, `ArticleModel`). Set `$table` to the module table name (`acl_users`, `blog_articles`). Prefer `$fillable` over `$guarded = []`; use a `casts()` method instead of a `$casts` property.
