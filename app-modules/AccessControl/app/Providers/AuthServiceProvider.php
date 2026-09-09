<?php

declare(strict_types=1);

namespace Modules\AccessControl\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\AccessControl\Models\PermissionModel;
use Modules\AccessControl\Models\Role\RoleModel;
use Modules\AccessControl\Models\UserModel;
use Modules\AccessControl\Policies\PermissionPolicy;
use Modules\AccessControl\Policies\RolePolicy;
use Modules\AccessControl\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(UserModel::class, UserPolicy::class);
        Gate::policy(RoleModel::class, RolePolicy::class);
        Gate::policy(PermissionModel::class, PermissionPolicy::class);
    }
}
