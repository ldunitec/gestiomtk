<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SyncPermissions extends Command
{
    protected $signature = 'permissions:sync {--guard=web}';
    protected $description = 'Crea permisos faltantes y los asigna al rol ADMINISTRADOR';

    public function handle()
    {
        $guard = $this->option('guard');

        $this->call('permission:cache-reset');
        $this->call('permission:from-routes');

        $role = Role::firstOrCreate([
            'name' => 'ADMINISTRADOR',
            'guard_name' => $guard,
        ]);

        $permissions = Permission::where('guard_name', $guard)->get();

        $added = [];

        foreach ($permissions as $permission) {
            if (! $role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
                $added[] = $permission->name;
            }
        }

        if (count($added)) {
            $this->info('Permisos asignados a ADMINISTRADOR:');
            foreach ($added as $name) {
                $this->line(" ✔ {$name}");
            }
        } else {
            $this->info('No hubo permisos nuevos para asignar.');
        }

        return Command::SUCCESS;
    }
}
