<?php

declare(strict_types=1);

namespace Modules\AccessControl\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AccessControl\Database\Factories\UserFactory;
use Modules\AccessControl\Models\Role\RoleEnum;
use Modules\AccessControl\Models\UserModel;

class SuperuserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserModel::unguard();

        $user = UserModel::firstOrCreate(
            ['id' => '00000000-0000-0000-0000-000000000000'],
            [
                ...UserFactory::new()->raw(),
                'name' => 'Superuser',
                'email' => 'superuser@example.com',
            ],
        );

        $user->assignRole(RoleEnum::SUPERUSER->value);

        UserModel::reguard();
    }
}
