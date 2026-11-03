<?php

namespace Modules\Mikrotik\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Permission, Role};
use Spatie\Permission\PermissionRegistrar;

class MikrotikDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $names = ['mikrotik.ver', 'mikrotik.configurar', 'mikrotik.fichas', 'mikrotik.sesiones'];
        foreach ($names as $name) {
            Permission::findOrCreate($name, 'web');
        }
        $admin = Role::where('name', 'ADMINISTRADOR')->where('guard_name', 'web')->first();
        $admin?->givePermissionTo($names);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
