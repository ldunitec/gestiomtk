<?php

namespace Modules\ActividadesTecnicas\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ActividadesTecnicasServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $root = dirname(__DIR__);
        $this->loadViewsFrom($root.'/Resources/views', 'actividades-tecnicas');
        $this->loadMigrationsFrom($root.'/Database/Migrations');
        $this->app->booted(function () use ($root) {
            if ($this->app->routesAreCached()) { return; }
            Route::middleware(['web','auth','can:actividades-tecnicas.ver'])
                ->prefix('admin/actividades-tecnicas')->name('actividades-tecnicas.')->group($root.'/Routes/web.php');
            Route::middleware(['api','auth:sanctum','can:actividades-tecnicas.ver','throttle:60,1'])
                ->prefix('api/v1/actividades-tecnicas')->name('api.actividades-tecnicas.')->group($root.'/Routes/api.php');
        });
    }
}
