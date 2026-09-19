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

use Illuminate\Support\Facades\Route;
use Modules\Impresion\Http\Controllers\CreyController;

Route::prefix('impresion')->group(function() {
    Route::get('/', 'ImpresionController@index');
});

Route::prefix('impresion/admin/creys')->name('impresion.admin.creys.')->group(function () {
    
    // GET: admin/exceldata
    Route::get('/', [CreyController::class, 'index'])->name('crey');
    Route::delete('/{crey}', [CreyController::class, 'destroy'])->name('destroy');
    Route::put('/{crey}', [CreyController::class, 'update'])->name('update');

    // POST: admin/exceldata
    Route::post('/importar', [CreyController::class, 'importar'])->name('importar');

    // POST: admin/exceldata/guardar
    Route::post('/guardar', [CreyController::class, 'guardar'])->name('guardar');

    // DELETE: admin/exceldata/todos
    Route::delete('/todos', [CreyController::class, 'eliminarTodos'])->name('eliminarTodos');

    // GET: admin/exceldata/pdf
    // Cambiado de crey_pdf a pdf para mayor limpieza en la URL
    Route::get('/pdf', [CreyController::class, 'imprimirPorCurso'])->name('excel_pdf');
});
