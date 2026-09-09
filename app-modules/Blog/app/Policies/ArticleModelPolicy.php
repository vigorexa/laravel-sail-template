<?php

declare(strict_types=1);

namespace Modules\Blog\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\AccessControl\Models\UserModel;
use Modules\Blog\Models\ArticleModel;

class ArticleModelPolicy
{
    use HandlesAuthorization;

    public function viewAny(UserModel $user): bool
    {
        return true;
    }

    public function view(UserModel $user, ArticleModel $articleModel): bool
    {
        return true;
    }

    public function create(UserModel $user): bool
    {
        return true;
    }

    public function update(UserModel $user, ArticleModel $articleModel): bool
    {
        return true;
    }

    public function delete(UserModel $user, ArticleModel $articleModel): bool
    {
        return true;
    }
}
