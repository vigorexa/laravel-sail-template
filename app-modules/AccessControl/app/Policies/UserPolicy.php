<?php

declare(strict_types=1);

namespace Modules\AccessControl\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\AccessControl\Models\UserModel;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(UserModel $user): ?bool
    {
        return $user->hasPermissionTo('access-control.user.view-any');
    }

    public function view(UserModel $user, UserModel $model)
    {
        return $user->hasPermissionTo("access-control.user.{$model->id}.view")
            || $user->id === $model->id;
    }

    public function update(UserModel $user, UserModel $model): bool
    {
        return $user->hasPermissionTo("access-control.user.{$model->id}.update")
            || $user->id === $model->id;
    }
}
