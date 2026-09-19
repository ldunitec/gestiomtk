<?php
namespace App\Http\Controllers\Finanzas;

use App\Http\Controllers\Controller;
use App\Models\Deuda;
use App\Models\Cuenta;
use Illuminate\Http\Request;

class DeudaController extends Controller
{
    public function index()
    {
        // $cuentas = Cuenta::all(); 
        return view('admin.finanzas.deudas.index'
        // , compact('cuentas')
        );
    }

    public function data()
    {
        $deudas = Deuda::with('cuenta')->get();

        return datatables()->of($deudas)
            ->addColumn('cuenta', fn($row) => $row->cuenta->nombre ?? 'Sin cuenta')
            ->addColumn('saldo_actual', fn($row) => number_format($row->saldo_actual, 2))
            ->addColumn('acciones', function($row){
                return '
                    <button class="btn btn-warning btn-sm editar" data-id="'.$row->id.'">Editar</button>
                    <button class="btn btn-danger btn-sm eliminar" data-id="'.$row->id.'">Eliminar</button>
                ';
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $deuda = Deuda::create([
            'nombre' => $request->nombre,
            'monto_total' => $request->monto_total,
            'saldo_actual' => $request->monto_total,
            'tasa_interes' => $request->tasa_interes ?? 0,
            'cuotas' => $request->cuotas,
            'cuota_mensual' => $request->cuota_mensual,
            'fecha_inicio' => $request->fecha_inicio,
            'tipo' => $request->tipo,
            'cuenta_id' => $request->cuenta_id,
        ]);

        return back()->with('success', 'Deuda registrada');
    }

    public function edit($id)
    {
        return Deuda::find($id);
    }

    public function update(Request $request, $id)
    {
        $deuda = Deuda::find($id);
        $deuda->update($request->all());

        return response()->json(['success' => 'ok']);
    }

    public function destroy($id)
    {
        Deuda::find($id)->delete();
        return response()->json(['success' => 'ok']);
    }

    public function show($id)
    {
        $deuda = Deuda::with('pagos')->find($id);
        return view('admin.finanzas.deudas.show', compact('deuda'));
    }
}