<?php

declare(strict_types=1);

namespace Modules\AccessControl\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\AccessControl\Database\Factories\UserFactory;
use Modules\AccessControl\Models\Role\RoleEnum;
use Modules\AccessControl\Models\UserModel;

class UserForEachRoleSeeder extends Seeder
{
    public function run(): void
    {
        UserModel::unguard();

        foreach (RoleEnum::cases() as $role) {
            $user = UserModel::firstOrCreate(
                ['email' => $email = Str::lower($role->value) . '@example.com'],
                [
                    ...UserFactory::new()->raw(),
                    'name' => $role->getLabel(),
                    'email' => $email,
                ],
            );

            $user->assignRole($role->value);
        }

        UserModel::reguard();
    }
}
