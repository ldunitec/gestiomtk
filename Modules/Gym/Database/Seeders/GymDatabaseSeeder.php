<?php

namespace Modules\Gym\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class GymDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $permissions = ['gym.ver', 'gym.administrar', 'gym.accesos', 'gym.reportes'];
        foreach ($permissions as $name) {
            Permission::findOrCreate($name, 'web');
        }
        $admin = Role::where('name', 'ADMINISTRADOR')->where('guard_name', 'web')->first();
        if ($admin) {
            $admin->givePermissionTo($permissions);
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
