<?php

namespace App\Http\Controllers\Finanzas;

use App\Http\Controllers\Controller;
use App\Models\Amortizacion;
use Illuminate\Http\Request;
use App\Models\Ingreso;
use App\Models\Egreso;
use App\Models\Deuda;

class DashboardController extends Controller
{


public function index()
{

$ingresos = Ingreso::sum('monto');
$egresos = Egreso::sum('monto');
// $alertas = Amortizacion::where('estado','pendiente')
// ->whereDate('fecha_pago','<=',now()->addDays(3))
// ->get();
$balance = $ingresos - $egresos;

$deudas = Deuda::count();

return view('admin.finanzas.dashboard',compact(
'ingresos',
'egresos',
'balance',
'deudas',
// 'alertas',
));

}
}
