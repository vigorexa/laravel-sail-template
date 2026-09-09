<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\AccessControl\Models\UserModel;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Gate::before(function (UserModel $user, string $ability): ?bool {
            if ($user->hasPermissionTo('*')) {
                return true;
            }

            return null;
        });
    }

    public function boot(): void
    {
        //
    }
}
