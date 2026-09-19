<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Modules\NetLan\Http\Controllers\DepartamentoController;
use Illuminate\Support\Facades\Route;
use Modules\NetLan\Http\Controllers\ConnectionController;
use Modules\NetLan\Http\Controllers\EquipoController;
use Modules\NetLan\Http\Controllers\EmpleadoController;
use Modules\NetLan\Http\Controllers\MarcaController;
use Modules\NetLan\Http\Controllers\ModeloController;
use Modules\NetLan\Http\Controllers\PatchPanelController;

Route::prefix('netlan')->group(function () {
    Route::get('/', 'NetLanController@index');
});




//rutas para marcas:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::


Route::middleware(['auth'])
    ->prefix('netlan/admin')
    ->as('netlan.admin.')
    ->group(function () {

        Route::resource('marcas', MarcaController::class);

    });
//rutas para modelos

Route::middleware(['auth'])
    ->prefix('netlan/admin')
    ->as('netlan.admin.')
    ->group(function () {

        Route::resource('modelos', ModeloController::class);

    });

// rutas para equipos 
Route::middleware(['auth'])
    ->prefix('netlan/admin')
    ->as('netlan.admin.')
    ->group(function () {

        Route::get('equipos/list', [EquipoController::class, 'list'])->name('equipos.list');
        // Route::get('monitoreo', [MonitoreoController::class, 'index'])->name('monitoreo.index');
// Route::get('monitoreo/data', [MonitoreoController::class, 'data'])->name('monitoreo.data');
// Route::get('alertas/data', [AlertaController::class, 'data'])->name('alertas.data');
// Route::get('alertas', [AlertaController::class, 'index'])->name('alertas.index');
// Route::get('monitoreo/dashboard-red', [DashboardRedController::class, 'index'])->name('monitoreo.dashboard.red');
// Route::get('monitoreo/dashboard-red/data', [DashboardRedController::class, 'data'])->name('dashboard.red.data');
    


        Route::resource('equipos', EquipoController::class);

    });

// rutas para departamentos 
Route::middleware(['auth'])
    ->prefix('netlan/admin')
    ->as('netlan.admin.')
    ->group(function () {

        Route::resource('departamentos', DepartamentoController::class);

    });
// rutas para patchPanel 
Route::middleware(['auth'])
    ->prefix('netlan/admin')
    ->as('netlan.admin.')
    ->group(function () {

        Route::resource('patchPanel', PatchPanelController::class);

    });
// rutas para empleados 
Route::middleware(['auth'])->prefix('netlan/admin')->as('netlan.admin.')
    ->group(function () {
        Route::get('/empleados/estadisticas', [EmpleadoController::class, 'estadisticas'])
        ->name('empleados.estadisticas');
        Route::get('/empleados/{id}/ficha', [EmpleadoController::class, 'ficha'])
            ->name('empleados.ficha');
        Route::resource('empleados', EmpleadoController::class);

    });



//rutas para conexiones
Route::middleware(['auth'])
    ->prefix('netlan/admin')
    ->as('netlan.admin.')
    ->group(function () {
        Route::get('/connections', [ConnectionController::class, 'index'])->name('connections.index');
        Route::get('/connections/create', [ConnectionController::class, 'create'])->name('connections.create');
        Route::post('/connections/create', [ConnectionController::class, 'store'])->name('connections.store');
        Route::get('/connections/show', [ConnectionController::class, 'show'])->name('connections.show');
        Route::get('/connections/{id}/edit', [ConnectionController::class, 'edit'])->name('connections.edit');
        Route::put('/connections/{id}', [ConnectionController::class, 'update'])->name('connections.update');
        Route::delete('/connections/{id}', [ConnectionController::class, 'destroy'])->name('connections.destroy');
        Route::get('/api/conexiones', [ConnectionController::class, 'apiConexiones'])->name('api.conexiones');
        Route::get('/connections/buscar-valor/{valor}', [ConnectionController::class, 'buscarValor']);
        Route::post('/connections/cambiar-estado/{id}', [ConnectionController::class, 'cambiarEstado']);
    });



//rutas para departamentos




//     Route::resource('equipos', EquipoController::class)->middleware('can:admin.equipos.index');

// });


// //rutas para paneles
// Route::middleware(['auth'])->group(function () {
//     Route::get('/admin/paneles', [App\Http\Controllers\PanelController::class, 'index'])->name('admin.paneles.index')->middleware('auth', 'can:admin.paneles.index');
//     Route::get('/admin/paneles/getModelos/{marca_id}', [App\Http\Controllers\PanelController::class, 'getModelos'])->name('admin.paneles.getModelos')->middleware('auth', 'can:admin.paneles.getModelos');
//     Route::get('/admin/paneles/create', [App\Http\Controllers\PanelController::class, 'create'])->name('admin.paneles.create')->middleware('auth', 'can:admin.paneles.create');
//     Route::post('/admin/paneles/create', [App\Http\Controllers\PanelController::class, 'store'])->name('admin.paneles.store')->middleware('auth', 'can:admin.paneles.store');
//     Route::get('/admin/paneles/{id}', [App\Http\Controllers\PanelController::class, 'show'])->name('admin.paneles.show')->middleware('auth', 'can:admin.paneles.show');
//     Route::get('/admin/paneles/{id}/edit', [App\Http\Controllers\PanelController::class, 'edit'])->name('admin.paneles.edit')->middleware('auth', 'can:admin.paneles.edit');
//     Route::put('/admin/paneles/{id}', [App\Http\Controllers\PanelController::class, 'update'])->name('admin.paneles.update')->middleware('auth', 'can:admin.paneles.update');
//     Route::delete('/admin/paneles/{id}', [App\Http\Controllers\PanelController::class, 'destroy'])->name('admin.paneles.destroy')->middleware('auth', 'can:admin.paneles.destroy');
//     Route::get('/admin/paneles/ping/{ip}', [PanelController::class, 'ping'])->name('admin.paneles.ping')->middleware('auth', 'can:admin.paneles.ping');
// });
// //rutas para patch paneles
// Route::middleware(['auth'])->group(function () {
//     Route::get('/admin/patch_panels', [App\Http\Controllers\PatchPanelController::class, 'index'])->name('admin.patch_panels.index')->middleware('auth', 'can:admin.patch_panels.index');
//     Route::get('/admin/patch_panels/create', [App\Http\Controllers\PatchPanelController::class, 'create'])->name('admin.patch_panels.create')->middleware('auth', 'can:admin.patch_panels.create');
//     Route::post('/admin/patch_panels/create', [App\Http\Controllers\PatchPanelController::class, 'store'])->name('admin.patch_panels.store')->middleware('auth', 'can:admin.patch_panels.store');
//     Route::get('/admin/patch_panels/{id}', [App\Http\Controllers\PatchPanelController::class, 'show'])->name('admin.patch_panels.show')->middleware('auth', 'can:admin.patch_panels.show');
//     Route::get('/admin/patch_panels/{id}/edit', [App\Http\Controllers\PatchPanelController::class, 'edit'])->name('admin.patch_panels.edit')->middleware('auth', 'can:admin.patch_panels.edit');
//     Route::put('/admin/patch_panels/{id}', [App\Http\Controllers\PatchPanelController::class, 'update'])->name('admin.patch_panels.update')->middleware('auth', 'can:admin.patch_panels.update');
//     Route::delete('/admin/patch_panels/{id}', [App\Http\Controllers\PatchPanelController::class, 'destroy'])->name('admin.patch_panels.destroy')->middleware('auth', 'can:admin.patch_panels.destroy');
// });
// //rutas para switch
// Route::middleware(['auth'])->group(function () {
//     Route::get('/admin/switchs', [App\Http\Controllers\SwitchMIKController::class, 'index'])->name('admin.switchs.index')->middleware('auth', 'can:admin.switchs.index');
//     Route::get('/admin/switchs/create', [App\Http\Controllers\SwitchMIKController::class, 'create'])->name('admin.switchs.create')->middleware('auth', 'can:admin.switchs.create');
//     Route::post('/admin/switchs/create', [App\Http\Controllers\SwitchMIKController::class, 'store'])->name('admin.switchs.store')->middleware('auth', 'can:admin.switchs.store');
//     Route::get('/admin/switchs/{id}', [App\Http\Controllers\SwitchMIKController::class, 'show'])->name('admin.switchs.show')->middleware('auth', 'can:admin.switchs.show');
//     Route::get('/admin/switchs/{id}/edit', [App\Http\Controllers\SwitchMIKController::class, 'edit'])->name('admin.switchs.edit')->middleware('auth', 'can:admin.switchs.edit');
//     Route::put('/admin/switchs/{id}', [App\Http\Controllers\SwitchMIKController::class, 'update'])->name('admin.switchs.update')->middleware('auth', 'can:admin.switchs.update');
//     Route::delete('/admin/switchs/{id}', [App\Http\Controllers\SwitchMIKController::class, 'destroy'])->name('admin.switchs.destroy')->middleware('auth', 'can:admin.switchs.destroy');
//     Route::get('/admin/switchs/sw/{id}', [App\Http\Controllers\SwitchMIKController::class, 'crear_puerto'])->name('admin.switchs.crear_puerto')->middleware('auth', 'can:admin.switchs.crear_puerto');
// });
//rutas para ports
Route::middleware(['auth'])->group(function () {
    //     Route::get('/admin/ports', [App\Http\Controllers\PortController::class, 'index'])->name('admin.ports.index')->middleware('auth', 'can:admin.ports.index');
//     Route::get('/admin/ports/create', [App\Http\Controllers\PortController::class, 'create'])->name('admin.ports.create')->middleware('auth', 'can:admin.ports.create');
//     Route::post('/admin/ports/create', [App\Http\Controllers\PortController::class, 'store'])->name('admin.ports.store')->middleware('auth', 'can:admin.ports.store');
//     Route::get('/admin/ports/{id}', [App\Http\Controllers\PortController::class, 'show'])->name('admin.ports.show')->middleware('auth', 'can:admin.ports.show');
//     Route::get('/admin/ports/{id}/edit', [App\Http\Controllers\PortController::class, 'edit'])->name('admin.ports.edit')->middleware('auth', 'can:admin.ports.edit');
//     Route::put('/admin/ports/{id}', [App\Http\Controllers\PortController::class, 'update'])->name('admin.ports.update')->middleware('auth', 'can:admin.ports.update');
//     Route::delete('/admin/ports/{id}', [App\Http\Controllers\PortController::class, 'destroy'])->name('admin.ports.destroy')->middleware('auth', 'can:admin.ports.destroy');
});