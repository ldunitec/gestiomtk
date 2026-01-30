<?php

use App\Http\Controllers\TemplateController;
use App\Http\Controllers\TemplateElementController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\DeviceModelController;
use App\Http\Controllers\DiagramaRedController;
use App\Http\Controllers\DispositivoController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\GanttController;
use App\Http\Controllers\MikrotikController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\PatchPanelController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\HotspotController;
use App\Http\Controllers\Rolecontroller;
use App\Http\Controllers\TareaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return redirect('/home');
// });

Auth::routes();
Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/register', function () {
    abort(403, 'Registro no permitido'); // O redirigir a otra ruta
})->name('register');
Route::view('/admin/tasks/agenda', 'admin.tasks.agenda');
Route::get('/home', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.index.home')->middleware('auth');


Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.index')->middleware('auth');

//rutas para configuraciones
Route::get('/admin/configuraciones', [App\Http\Controllers\ConfiguracionController::class, 'index'])->name('admin.configuracion.index')->middleware('auth', 'can:admin.configuracion.index');
Route::post('/admin/configuraciones/create', [App\Http\Controllers\ConfiguracionController::class, 'store'])->name('admin.configuracion.store')->middleware('auth', 'can:admin.configuracion.store');


//rutas para roles
Route::get('/admin/roles', [App\Http\Controllers\Rolecontroller::class, 'index'])->name('admin.roles.index')->middleware('auth', 'can:admin.roles.index');
Route::get('/admin/roles/create', [App\Http\Controllers\Rolecontroller::class, 'create'])->name('admin.roles.create')->middleware('auth', 'can:admin.roles.create');
Route::post('/admin/roles/create', [App\Http\Controllers\Rolecontroller::class, 'store'])->name('admin.roles.store')->middleware('auth', 'can:admin.roles.store');
Route::get('/admin/roles/{id}', [App\Http\Controllers\Rolecontroller::class, 'show'])->name('admin.roles.show')->middleware('auth', 'can:admin.roles.show');
Route::get('/admin/roles/{id}/edit', [App\Http\Controllers\Rolecontroller::class, 'edit'])->name('admin.roles.edit')->middleware('auth', 'can:admin.roles.edit');
Route::put('/admin/roles/{id}', [App\Http\Controllers\Rolecontroller::class, 'update'])->name('admin.roles.update')->middleware('auth', 'can:admin.roles.update');
Route::delete('/admin/roles/{id}', [App\Http\Controllers\Rolecontroller::class, 'destroy'])->name('admin.roles.destroy')->middleware('auth', 'can:admin.roles.destroy');
Route::put('/admin/roles/{id}/permisos', [App\Http\Controllers\Rolecontroller::class, 'actualizarPermisos'])->name('admin.roles.actualizarPermisos')->middleware('auth', 'can:admin.roles.actualizarPermisos');
Route::get('/admin/roles/{id}', [App\Http\Controllers\Rolecontroller::class, 'permisos'])->name('admin.roles.permisos')->middleware('auth', 'can:admin.roles.permisos');


Route::get('/admin/generate-permissions', [Rolecontroller::class, 'generate'])
    ->name('admin.generate-permissions')
    ->middleware(['auth']); // solo admins

//rutas para administrativos
Route::get('/admin/administrativos', [App\Http\Controllers\AdministrativoController::class, 'index'])->name('admin.administrativos.index')->middleware('auth', 'can:admin.administrativos.index');
Route::get('/admin/administrativos/create', [App\Http\Controllers\AdministrativoController::class, 'create'])->name('admin.administrativos.create')->middleware('auth', 'can:admin.administrativos.create');
Route::post('/admin/administrativos/create', [App\Http\Controllers\AdministrativoController::class, 'store'])->name('admin.administrativos.store')->middleware('auth', 'can:admin.administrativos.store');
Route::get('/admin/administrativos/{id}', [App\Http\Controllers\AdministrativoController::class, 'show'])->name('admin.administrativos.show')->middleware('auth', 'can:admin.administrativos.show');
Route::get('/admin/administrativos/{id}/edit', [App\Http\Controllers\AdministrativoController::class, 'edit'])->name('admin.administrativos.edit')->middleware('auth', 'can:admin.administrativos.edit');
Route::put('/admin/administrativos/{id}', [App\Http\Controllers\AdministrativoController::class, 'update'])->name('admin.administrativos.update')->middleware('auth', 'can:admin.administrativos.update');
Route::delete('/admin/administrativos/{id}', [App\Http\Controllers\AdministrativoController::class, 'destroy'])->name('admin.administrativos.destroy')->middleware('auth', 'can:admin.administrativos.destroy');



//rutas para marcas:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
Route::get('/admin/marcas', [App\Http\Controllers\MarcaController::class, 'index'])->name('admin.marcas.index')->middleware('auth', 'can:admin.marcas.index');
Route::get('/admin/marcas/create', [App\Http\Controllers\MarcaController::class, 'create'])->name('admin.marcas.create')->middleware('auth', 'can:admin.marcas.create');
Route::post('/admin/marcas/create', [App\Http\Controllers\MarcaController::class, 'store'])->name('admin.marcas.store')->middleware('auth', 'can:admin.marcas.store');
Route::get('/admin/marcas/{id}', [App\Http\Controllers\MarcaController::class, 'show'])->name('admin.marcas.show')->middleware('auth', 'can:admin.marcas.show');
Route::get('/admin/marcas/{id}/edit', [App\Http\Controllers\MarcaController::class, 'edit'])->name('admin.marcas.edit')->middleware('auth', 'can:admin.marcas.edit');
Route::put('/admin/marcas/{id}', [App\Http\Controllers\MarcaController::class, 'update'])->name('admin.marcas.update')->middleware('auth', 'can:admin.marcas.update');
Route::delete('/admin/marcas/{id}', [App\Http\Controllers\MarcaController::class, 'destroy'])->name('admin.marcas.destroy')->middleware('auth', 'can:admin.marcas.destroy');

//rutas para modelos
Route::get('/admin/modelos', [App\Http\Controllers\ModeloController::class, 'index'])->name('admin.modelos.index')->middleware('auth', 'can:admin.modelos.index');
Route::get('/admin/modelos/create', [App\Http\Controllers\ModeloController::class, 'create'])->name('admin.modelos.create')->middleware('auth', 'can:admin.modelos.create');
Route::post('/admin/modelos/create', [App\Http\Controllers\ModeloController::class, 'store'])->name('admin.modelos.store')->middleware('auth', 'can:admin.modelos.store');
Route::get('/admin/modelos/{id}', [App\Http\Controllers\ModeloController::class, 'show'])->name('admin.modelos.show')->middleware('auth', 'can:admin.modelos.show');
Route::get('/admin/modelos/{id}/edit', [App\Http\Controllers\ModeloController::class, 'edit'])->name('admin.modelos.edit')->middleware('auth', 'can:admin.modelos.edit');
Route::put('/admin/modelos/{id}', [App\Http\Controllers\ModeloController::class, 'update'])->name('admin.modelos.update')->middleware('auth', 'can:admin.modelos.update');
Route::delete('/admin/modelos/{id}', [App\Http\Controllers\ModeloController::class, 'destroy'])->name('admin.modelos.destroy')->middleware('auth', 'can:admin.modelos.destroy');

//rutas para departamentos
Route::get('/admin/departamentos', [App\Http\Controllers\DepartamentoController::class, 'index'])->name('admin.departamentos.index')->middleware('auth', 'can:admin.departamentos.index');
Route::get('/admin/departamentos/create', [App\Http\Controllers\DepartamentoController::class, 'create'])->name('admin.departamentos.create')->middleware('auth', 'can:admin.departamentos.create');
Route::post('/admin/departamentos/create', [App\Http\Controllers\DepartamentoController::class, 'store'])->name('admin.departamentos.store')->middleware('auth', 'can:admin.departamentos.store');
Route::get('/admin/departamentos/{id}', [App\Http\Controllers\DepartamentoController::class, 'show'])->name('admin.departamentos.show')->middleware('auth', 'can:admin.departamentos.show');
Route::get('/admin/departamentos/{id}/edit', [App\Http\Controllers\DepartamentoController::class, 'edit'])->name('admin.departamentos.edit')->middleware('auth', 'can:admin.departamentos.edit');
Route::put('/admin/departamentos/{id}', [App\Http\Controllers\DepartamentoController::class, 'update'])->name('admin.departamentos.update')->middleware('auth', 'can:admin.departamentos.update');
Route::delete('/admin/departamentos/{id}', [App\Http\Controllers\DepartamentoController::class, 'destroy'])->name('admin.departamentos.destroy')->middleware('auth', 'can:admin.departamentos.destroy');

//rutas para empleados
Route::get('/admin/empleados', [App\Http\Controllers\EmpleadoController::class, 'index'])->name('admin.empleados.index')->middleware('auth', 'can:admin.empleados.index');
Route::get('/admin/empleados/create', [App\Http\Controllers\EmpleadoController::class, 'create'])->name('admin.empleados.create')->middleware('auth', 'can:admin.empleados.create');
Route::post('/admin/empleados/create', [App\Http\Controllers\EmpleadoController::class, 'store'])->name('admin.empleados.store')->middleware('auth', 'can:admin.empleados.store');
Route::get('/admin/empleados/{id}', [App\Http\Controllers\EmpleadoController::class, 'show'])->name('admin.empleados.show')->middleware('auth', 'can:admin.empleados.show');
Route::get('/admin/empleados/{id}/edit', [App\Http\Controllers\EmpleadoController::class, 'edit'])->name('admin.empleados.edit')->middleware('auth', 'can:admin.empleados.edit');
Route::put('/admin/empleados/{id}', [App\Http\Controllers\EmpleadoController::class, 'update'])->name('admin.empleados.update')->middleware('auth', 'can:admin.empleados.update');
Route::delete('/admin/empleados/{id}', [App\Http\Controllers\EmpleadoController::class, 'destroy'])->name('admin.empleados.destroy')->middleware('auth', 'can:admin.empleados.destroy');



//rutas para equipos
Route::get('/admin/equipos', [App\Http\Controllers\EquipoController::class, 'index'])->name('admin.equipos.index')->middleware('auth', 'can:admin.equipos.index');
Route::get('/admin/equipos/getModelos/{marca_id}', [App\Http\Controllers\EquipoController::class, 'getModelos'])->name('admin.equipos.getModelos')->middleware('auth', 'can:admin.equipos.getModelos');
Route::get('/admin/equipos/create', [App\Http\Controllers\EquipoController::class, 'create'])->name('admin.equipos.create')->middleware('auth', 'can:admin.equipos.create');
Route::post('/admin/equipos/create', [App\Http\Controllers\EquipoController::class, 'store'])->name('admin.equipos.store')->middleware('auth', 'can:admin.equipos.store');
Route::get('/admin/equipos/{id}', [App\Http\Controllers\EquipoController::class, 'show'])->name('admin.equipos.show')->middleware('auth', 'can:admin.equipos.show');
Route::get('/admin/equipos/{id}/edit', [App\Http\Controllers\EquipoController::class, 'edit'])->name('admin.equipos.edit')->middleware('auth', 'can:admin.equipos.edit');
Route::put('/admin/equipos/{id}', [App\Http\Controllers\EquipoController::class, 'update'])->name('admin.equipos.update')->middleware('auth', 'can:admin.equipos.update');
Route::delete('/admin/equipos/{id}', [App\Http\Controllers\EquipoController::class, 'destroy'])->name('admin.equipos.destroy')->middleware('auth', 'can:admin.equipos.destroy');
Route::post('/admin/equipos/cambiar-estado/{id}', [EquipoController::class, 'cambiarEstado'])->name('admin.equipos.cambiarEstado')->middleware('auth', 'can:admin.equipos.cambiarEstado');

//rutas para paneles
Route::get('/admin/paneles', [App\Http\Controllers\PanelController::class, 'index'])->name('admin.paneles.index')->middleware('auth', 'can:admin.paneles.index');
Route::get('/admin/paneles/getModelos/{marca_id}', [App\Http\Controllers\PanelController::class, 'getModelos'])->name('admin.paneles.getModelos')->middleware('auth', 'can:admin.paneles.getModelos');
Route::get('/admin/paneles/create', [App\Http\Controllers\PanelController::class, 'create'])->name('admin.paneles.create')->middleware('auth', 'can:admin.paneles.create');
Route::post('/admin/paneles/create', [App\Http\Controllers\PanelController::class, 'store'])->name('admin.paneles.store')->middleware('auth', 'can:admin.paneles.store');
Route::get('/admin/paneles/{id}', [App\Http\Controllers\PanelController::class, 'show'])->name('admin.paneles.show')->middleware('auth', 'can:admin.paneles.show');
Route::get('/admin/paneles/{id}/edit', [App\Http\Controllers\PanelController::class, 'edit'])->name('admin.paneles.edit')->middleware('auth', 'can:admin.paneles.edit');
Route::put('/admin/paneles/{id}', [App\Http\Controllers\PanelController::class, 'update'])->name('admin.paneles.update')->middleware('auth', 'can:admin.paneles.update');
Route::delete('/admin/paneles/{id}', [App\Http\Controllers\PanelController::class, 'destroy'])->name('admin.paneles.destroy')->middleware('auth', 'can:admin.paneles.destroy');
Route::get('/admin/paneles/ping/{ip}', [PanelController::class, 'ping'])->name('admin.paneles.ping')->middleware('auth', 'can:admin.paneles.ping');

//rutas para patch paneles
Route::get('/admin/patch_panels', [App\Http\Controllers\PatchPanelController::class, 'index'])->name('admin.patch_panels.index')->middleware('auth', 'can:admin.patch_panels.index');
Route::get('/admin/patch_panels/create', [App\Http\Controllers\PatchPanelController::class, 'create'])->name('admin.patch_panels.create')->middleware('auth', 'can:admin.patch_panels.create');
Route::post('/admin/patch_panels/create', [App\Http\Controllers\PatchPanelController::class, 'store'])->name('admin.patch_panels.store')->middleware('auth', 'can:admin.patch_panels.store');
Route::get('/admin/patch_panels/{id}', [App\Http\Controllers\PatchPanelController::class, 'show'])->name('admin.patch_panels.show')->middleware('auth', 'can:admin.patch_panels.show');
Route::get('/admin/patch_panels/{id}/edit', [App\Http\Controllers\PatchPanelController::class, 'edit'])->name('admin.patch_panels.edit')->middleware('auth', 'can:admin.patch_panels.edit');
Route::put('/admin/patch_panels/{id}', [App\Http\Controllers\PatchPanelController::class, 'update'])->name('admin.patch_panels.update')->middleware('auth', 'can:admin.patch_panels.update');
Route::delete('/admin/patch_panels/{id}', [App\Http\Controllers\PatchPanelController::class, 'destroy'])->name('admin.patch_panels.destroy')->middleware('auth', 'can:admin.patch_panels.destroy');


//rutas para switch
Route::get('/admin/switchs', [App\Http\Controllers\SwitchMIKController::class, 'index'])->name('admin.switchs.index')->middleware('auth', 'can:admin.switchs.index');
Route::get('/admin/switchs/create', [App\Http\Controllers\SwitchMIKController::class, 'create'])->name('admin.switchs.create')->middleware('auth', 'can:admin.switchs.create');
Route::post('/admin/switchs/create', [App\Http\Controllers\SwitchMIKController::class, 'store'])->name('admin.switchs.store')->middleware('auth', 'can:admin.switchs.store');
Route::get('/admin/switchs/{id}', [App\Http\Controllers\SwitchMIKController::class, 'show'])->name('admin.switchs.show')->middleware('auth', 'can:admin.switchs.show');
Route::get('/admin/switchs/{id}/edit', [App\Http\Controllers\SwitchMIKController::class, 'edit'])->name('admin.switchs.edit')->middleware('auth', 'can:admin.switchs.edit');
Route::put('/admin/switchs/{id}', [App\Http\Controllers\SwitchMIKController::class, 'update'])->name('admin.switchs.update')->middleware('auth', 'can:admin.switchs.update');
Route::delete('/admin/switchs/{id}', [App\Http\Controllers\SwitchMIKController::class, 'destroy'])->name('admin.switchs.destroy')->middleware('auth', 'can:admin.switchs.destroy');
Route::get('/admin/switchs/sw/{id}', [App\Http\Controllers\SwitchMIKController::class, 'crear_puerto'])->name('admin.switchs.crear_puerto')->middleware('auth', 'can:admin.switchs.crear_puerto');


//rutas para ports
Route::get('/admin/ports', [App\Http\Controllers\PortController::class, 'index'])->name('admin.ports.index')->middleware('auth', 'can:admin.ports.index');
Route::get('/admin/ports/create', [App\Http\Controllers\PortController::class, 'create'])->name('admin.ports.create')->middleware('auth', 'can:admin.ports.create');
Route::post('/admin/ports/create', [App\Http\Controllers\PortController::class, 'store'])->name('admin.ports.store')->middleware('auth', 'can:admin.ports.store');
Route::get('/admin/ports/{id}', [App\Http\Controllers\PortController::class, 'show'])->name('admin.ports.show')->middleware('auth', 'can:admin.ports.show');
Route::get('/admin/ports/{id}/edit', [App\Http\Controllers\PortController::class, 'edit'])->name('admin.ports.edit')->middleware('auth', 'can:admin.ports.edit');
Route::put('/admin/ports/{id}', [App\Http\Controllers\PortController::class, 'update'])->name('admin.ports.update')->middleware('auth', 'can:admin.ports.update');
Route::delete('/admin/ports/{id}', [App\Http\Controllers\PortController::class, 'destroy'])->name('admin.ports.destroy')->middleware('auth', 'can:admin.ports.destroy');

//rutas para conexiones
Route::get('/admin/connections', [App\Http\Controllers\ConnectionController::class, 'index'])->name('admin.connections.index')->middleware('auth', 'can:admin.connections.index');
Route::get('/admin/connections/create', [App\Http\Controllers\ConnectionController::class, 'create'])->name('admin.connections.create')->middleware('auth', 'can:admin.connections.create');
Route::post('/admin/connections/create', [App\Http\Controllers\ConnectionController::class, 'store'])->name('admin.connections.store')->middleware('auth', 'can:admin.connections.store');
Route::get('/admin/connections/show', [App\Http\Controllers\ConnectionController::class, 'show'])->name('admin.connections.show')->middleware('auth', 'can:admin.connections.show');
Route::get('/admin/connections/{id}/edit', [App\Http\Controllers\ConnectionController::class, 'edit'])->name('admin.connections.edit')->middleware('auth', 'can:admin.connections.edit');
Route::put('/admin/connections/{id}', [App\Http\Controllers\ConnectionController::class, 'update'])->name('admin.connections.update')->middleware('auth', 'can:admin.connections.update');
Route::delete('/admin/connections/{id}', [App\Http\Controllers\ConnectionController::class, 'destroy'])->name('admin.connections.destroy')->middleware('auth', 'can:admin.connections.destroy');
Route::get('/api/conexiones', [\App\Http\Controllers\ConnectionController::class, 'apiConexiones'])->name('api.conexiones')->middleware('auth', 'can:admin.connections.apiConexiones');
Route::get('/admin/connections/buscar-valor/{valor}', [App\Http\Controllers\ConnectionController::class, 'buscarValor']);
Route::post('/admin/connections/cambiar-estado/{id}', [\App\Http\Controllers\ConnectionController::class, 'cambiarEstado']);



// rutas para backup 
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/backups', [BackupController::class, 'index'])->name('admin.backups.index')->middleware('auth');
    // Route::get('/admin/backups', [BackupController::class, 'index'])->name('admin.backups.index')->middleware('auth','can:admin.backups.index');
    Route::post('/admin/backups/create', [BackupController::class, 'create'])->name('admin.backups.create')->middleware('auth');
    Route::get('/admin/backups/download/{filename}', [BackupController::class, 'download'])->name('admin.backups.download')->middleware('auth');
});



// rutas agenda
Route::get('/admin/agenda', [AgendaController::class, 'index'])->name('admin.agenda.index')->middleware('auth', 'can:admin.agenda.index');
Route::post('/admin/agenda', [AgendaController::class, 'store'])->name('admin.agenda.store')->middleware('auth', 'can:admin.agenda.store');
Route::get('/admin/agenda/{id}', [AgendaController::class, 'show'])->name('admin.agenda.show')->middleware('auth', 'can:admin.agenda.show');
Route::put('/agenda/{event}/update', [AgendaController::class, 'update'])->name('admin.agenda.update')->middleware('auth', 'can:admin.agenda.update');
Route::put('/admin/agenda/updateNoAgendado/{id}', [AgendaController::class, 'updateNoAgendado'])->name('admin.agenda.updateNoAgendado')->middleware('auth', 'can:admin.agenda.updateNoAgendado');
Route::put('/admin/agenda/completar/{id}', [AgendaController::class, 'completar'])->name('admin.agenda.completar')->middleware('auth', 'can:admin.agenda.completar');
Route::delete('/admin/agenda/{id}', [AgendaController::class, 'destroy'])->name('admin.agenda.destroy')->middleware('auth', 'can:admin.agenda.destroy');

Route::post('/admin/agenda/{event}/update-date', [AgendaController::class, 'updateDate'])->name('admin.agenda.updateDate')->middleware('auth', 'can:admin.agenda.updateDate');
Route::post('/admin/agenda/{event}/update-item', [AgendaController::class, 'updateDateItem'])->name('admin.agenda.updateDateItem')->middleware('auth', 'can:admin.agenda.updateDateItem');
Route::post('/admin/agenda/{event}/update-estate', [AgendaController::class, 'updateEstado'])->name('admin.agenda.updateEstado')->middleware('auth', 'can:admin.agenda.updateEstado');

// ruta de archivos y driver 
// Route::resource('drivers', DriverController::class);

Route::get('/admin/drivers', [App\Http\Controllers\DriverController::class, 'index'])->name('admin.drivers.index')->middleware('auth, can:admin.drivers.index');
Route::get('/admin/drivers/create', [App\Http\Controllers\DriverController::class, 'create'])->name('admin.drivers.create')->middleware('auth, can:admin.drivers.create');
Route::post('/admin/drivers/create', [App\Http\Controllers\DriverController::class, 'store'])->name('admin.drivers.store')->middleware('auth, can:admin.drivers.store');
Route::get('/admin/drivers/{id}', [App\Http\Controllers\DriverController::class, 'show'])->name('admin.drivers.show')->middleware('auth, can:admin.drivers.show');
Route::get('/admin/drivers/{id}/edit', [App\Http\Controllers\DriverController::class, 'edit'])->name('admin.drivers.edit')->middleware('auth, can:admin.drivers.edit');
Route::put('/admin/drivers/{id}', [App\Http\Controllers\DriverController::class, 'update'])->name('admin.drivers.update')->middleware('auth, can:admin.drivers.update');
Route::delete('/admin/drivers/{id}', [App\Http\Controllers\DriverController::class, 'destroy'])->name('admin.drivers.destroy')->middleware('auth, can:admin.drivers.destroy');



Route::get('drivers/download/{file}', [DriverController::class, 'download'])->name('drivers.download')->middleware('auth, can:drivers.download');


// rutas alternas 


Route::post('categories/ajax', [CategoryController::class, 'storeAjax'])->name('categories.ajax.store')->middleware('auth, can:categories.ajax.store');

Route::post('models/ajax', [DeviceModelController::class, 'storeAjax'])->name('models.ajax.store')->middleware('auth, can:models.ajax.store');
;
;


// rutas de archivos 
Route::get('/admin/files', [App\Http\Controllers\FileController::class, 'index'])->name('admin.files.index')->middleware('auth, can:admin.files.index');
Route::get('/admin/files/create', [App\Http\Controllers\FileController::class, 'create'])->name('admin.files.create')->middleware('auth');
Route::post('/admin/files/create', [App\Http\Controllers\FileController::class, 'store'])->name('admin.files.store')->middleware('auth');
Route::get('/admin/files/{id}', [App\Http\Controllers\FileController::class, 'show'])->name('admin.files.show')->middleware('auth, can:admin.files.show');
Route::get('/admin/files/{id}/edit', [App\Http\Controllers\FileController::class, 'edit'])->name('admin.files.edit')->middleware('auth, can:admin.files.edit');
Route::put('/admin/files/{id}', [App\Http\Controllers\FileController::class, 'update'])->name('admin.files.update')->middleware('auth, can:admin.files.update');
Route::delete('/admin/files/{id}', [App\Http\Controllers\FileController::class, 'destroy'])->name('admin.files.destroy')->middleware('auth, can:admin.files.destroy');
Route::delete('/admin/files/{id}', [App\Http\Controllers\DriverController::class, 'destroy'])->name('admin.files.destroy')->middleware('auth, can:admin.files.destroy');




// ruta brands 

Route::get('admin/brands/{brand}', function (App\Models\Brand $brand) {return $brand; });

// routes/web.php
Route::prefix('admin')->group(function () {

    Route::get('brands', [BrandController::class, 'index'])->name('admin.brands.index')->middleware('auth');
    Route::get('brands/{brand}', [BrandController::class, 'show'])->name('admin.brands.show')->middleware('auth');
    Route::post('brands/store', [BrandController::class, 'store'])->name('admin.brands.store')->middleware('auth');
    Route::post('notes', [NoteController::class, 'store'])->name('admin.notes.store')->middleware('auth, can:admin.notes.store');
    Route::get('files/{file}/download', [FileController::class, 'download'])->name('files.download')->middleware('auth, can:files.download');

});
// marcaciones aplicando una forma de establecer permisos ->middleware(['auth', 'can:admin.marcaciones.index']);
Route::get('admin/marcaciones', [App\Http\Controllers\AsistenciaController::class, 'index'])->name('admin.marcaciones.index')->middleware(['auth', 'can:admin.marcaciones.index']);
Route::post('admin/marcaciones/importar', [App\Http\Controllers\AsistenciaController::class, 'importar'])->name('admin.marcaciones.importar')->middleware(['auth', 'can:admin.marcaciones.importar']);
Route::post('admin/marcaciones/configurar', [App\Http\Controllers\AsistenciaController::class, 'configurar'])->name('admin.marcaciones.configurar')->middleware(['auth', 'can:admin.marcaciones.configurar']);



// mikrotik 
Route::middleware(['auth'])->prefix('admin')->group(function () {

    // GET → mostrar formulario
    Route::get('mikrotik/autorizar', function () {
        return view('admin.mikrotik.autorizar'); })->name('admin.mikrotik.form');
    // POST → procesar autorización
    Route::post('mikrotik/autorizar', [MikrotikController::class, 'autorizar'])->name('admin.mikrotik.autorizar');
});

Route::middleware(['auth'])->prefix('admin')->group(function () {

    Route::get('mikrotik/clientes', [MikrotikController::class, 'index'])->name('admin.mikrotik.clientes');
    Route::post('mikrotik/lease/toggle', [MikrotikController::class, 'toggleLease'])->name('admin.mikrotik.toggle');
});

// host online 
Route::get('admin/mikrotik/conectados', [MikrotikController::class, 'conectados'])->name('admin.mikrotik.conectados')->middleware('auth');

// visio

Route::middleware(['auth'])->prefix('admin/diagramas/red')->group(function () {
    Route::get('/', [DiagramaRedController::class, 'index'])->name('red.index');
    Route::post('posicion', [DiagramaRedController::class, 'guardarPosicion']);
    Route::post('equipo', [DiagramaRedController::class, 'storeEquipo']);
    Route::post('conexion', [DiagramaRedController::class, 'storeConexion']);
});

use App\Http\Controllers\InventarioBTRController;
use App\Http\Controllers\NotaController;

Route::get('/admin/inventarioBTR', [InventarioBTRController::class, 'index'])->name('admin.inventarioBTR.index');
Route::post('/admin/inventarioBTR', [InventarioBTRController::class, 'store'])->name('admin.inventarioBTR.store');



Route::get('/admin/hotspot/login',[App\Http\Controllers\HotspotController::class, 'login'])->name('admin.hotspot.login');
Route::post('/admin/hotspot/login', [App\Http\Controllers\HotspotController::class, 'auth'])->name('admin.hotspot.auth');



// NOtas 
Route::get('/admin/notas', [App\Http\Controllers\NotaController::class, 'index'])->name('admin.notas.index')->middleware([ 'auth', 'can:admin.notas.index']);
// Route::get('/admin/notas/create', [App\Http\Controllers\NotaController::class, 'create'])->name('admin.notas.create')->middleware([ 'auth', 'can:admin.notas.create']);
Route::post('/admin/notas/create', [App\Http\Controllers\NotaController::class, 'store'])->name('admin.notas.store')->middleware([ 'auth', 'can:admin.notas.store']);
Route::get('/admin/notas/{id}', [App\Http\Controllers\NotaController::class, 'show'])->name('admin.notas.show')->middleware([ 'auth', 'can:admin.notas.show']);
Route::get('/admin/notas/{id}/edit', [App\Http\Controllers\NotaController::class, 'edit'])->name('admin.notas.edit')->middleware([ 'auth', 'can:admin.notas.edit']);
Route::put('/admin/notas/{id}', [App\Http\Controllers\NotaController::class, 'update'])->name('admin.notas.update')->middleware([ 'auth', 'can:admin.notas.update']);
Route::delete('/admin/notas/{id}', [App\Http\Controllers\NotaController::class, 'destroy'])->name('admin.notas.destroy')->middleware([ 'auth', 'can:admin.notas.destroy']);


// rytas gant
Route::get('/admin/gantt', [GanttController::class, 'index'])->name('admin.gantt.index');


