<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class GeneratePermissionsFromRoutes extends Command
{
    protected $signature = 'permission:from-routes';
    protected $description = 'Genera permisos automáticamente desde rutas con name';

    public function handle()
    {
        $routes = Route::getRoutes();
        $count = 0;

        foreach ($routes as $route) {
            $name = $route->getName();

            if (!$name) {
                continue;
            }

            // Solo rutas admin (opcional)
            if (!str_starts_with($name, 'ADMINISTRADOR.')) {
                continue;
            }

            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);

            $count++;
        }

        $this->info("✅ {$count} permisos generados correctamente");
        return 0;
    }
}
