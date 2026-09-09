# Access Control Module

> Модуль управления пользователями, ролями и правами.

## Модели

- **`UserModel`** (`acl_users`) — пользователь приложения. Spatie Permission для ролей и прав, Filament для админ-панели.
- **`RoleModel`** / **`PermissionModel`** — роли и права (Spatie Laravel Permission).
  - Права именуются по шаблону `access-control.user.view-any`, `access-control.user.{uuid}.view` и т.д. Право `*` даёт полный доступ (`App\Providers\AuthServiceProvider`).
