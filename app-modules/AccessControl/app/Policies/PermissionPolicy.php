<?php

declare(strict_types=1);

namespace Modules\AccessControl\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\AccessControl\Models\UserModel;

class PermissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(UserModel $user): ?bool
    {
        return $user->hasPermissionTo('access-control.permission.view-any');
    }
}
