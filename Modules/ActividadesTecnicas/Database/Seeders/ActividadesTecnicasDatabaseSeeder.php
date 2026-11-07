<?php

namespace Modules\ActividadesTecnicas\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Permission, Role};
use Spatie\Permission\PermissionRegistrar;

class ActividadesTecnicasDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $permissions = ['actividades-tecnicas.ver','actividades-tecnicas.gestionar','actividades-tecnicas.cancelar'];
        foreach ($permissions as $name) { Permission::findOrCreate($name, 'web'); }
        $admin = Role::where('name', 'ADMINISTRADOR')->where('guard_name', 'web')->first();
        if ($admin) { $admin->givePermissionTo($permissions); }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
