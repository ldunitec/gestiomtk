<?php

namespace Modules\Mikrotik\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Mikrotik\Console\ConciliarFichas;

class MikrotikServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $root = dirname(__DIR__);
        $this->loadViewsFrom($root.'/Resources/views', 'mikrotik');
        $this->loadMigrationsFrom($root.'/Database/Migrations');
        $this->mergeConfigFrom($root.'/Config/config.php', 'mikrotik_visitantes');
        $this->commands([ConciliarFichas::class]);
        $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('mikrotik:conciliar')->everyMinute()->withoutOverlapping(5);
        });
        $this->app->booted(function () use ($root) {
            if (!$this->app->routesAreCached()) {
                Route::middleware(['web', 'auth'])->prefix('routers-mikrotik')->name('mt.')->group($root.'/Routes/web.php');
            }
        });
    }
}
