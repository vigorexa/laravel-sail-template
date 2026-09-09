<?php

declare(strict_types=1);

namespace Modules\AccessControl\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AccessControl\Models\PermissionModel;
use Modules\AccessControl\Models\Role\RoleEnum;
use Modules\AccessControl\Models\Role\RoleModel;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RoleModel::unguard();

        foreach (RoleEnum::cases() as $role) {
            RoleModel::firstOrCreate(['id' => $role->value], ['name' => $role->getLabel()]);
        }

        $everythingPermission = PermissionModel::firstOrCreate(['name' => '*']);
        RoleModel::findById('superuser')->givePermissionTo($everythingPermission);

        RoleModel::reguard();
    }
}
