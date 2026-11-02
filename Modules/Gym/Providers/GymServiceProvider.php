<?php

namespace Modules\Gym\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class GymServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $root = dirname(__DIR__);
        $this->loadViewsFrom($root.'/Resources/views', 'gym');
        $this->loadMigrationsFrom($root.'/Database/Migrations');
        $this->mergeConfigFrom($root.'/Config/config.php', 'gym');
        $this->app->booted(function () use ($root) {
            if ($this->app->routesAreCached()) {
                return;
            }
            Route::middleware(['web', 'auth'])->prefix('gym')->name('gym.')->group($root.'/Routes/web.php');
            Route::middleware(['api', 'auth:sanctum', 'can:gym.accesos', 'throttle:120,1'])->prefix('api/gym')->name('gym.api.')->group($root.'/Routes/api.php');
        });
    }
}
