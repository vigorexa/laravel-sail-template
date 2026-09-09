---
paths:
  - 'app-modules/**/app/Policies/**'
---

# Policies

## Policies registered with UsePolicy on models
Attach policies to Eloquent models with `#[UsePolicy(...Policy::class)]` on the model class. Policy methods type-hint `UserModel` as the authenticated user, not `Authenticatable` or `User`.
