<?php

namespace App\Http\Controllers;

use App\Models\Liquidacion;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;


class LiquidacionController extends Controller
{

    public function index()
    {

        $liquidaciones = Liquidacion::with('gastos')->latest()->get();
        return view('admin.liquidaciones.index', compact('liquidaciones'));
    }
    public function store(Request $request)
    {
        $liquidacion = Liquidacion::create([
            'ciudad' => $request->ciudad,
            'fecha_entrega' => $request->fecha_entrega,
            'asignado' => $request->asignado,
            'total' => $request->total,
            'deposito' => $request->deposito,
            'solicitud_id' => $request->solicitud_id,
            'estado' => '1'
        ]);

        foreach ($request->gastos as $gasto) {
            $liquidacion->gastos()->create($gasto);
        }

        if ($request->solicitud_id) {
            Solicitud::where('id', $request->solicitud_id)
                ->update(['estado' => 'procesado']);
        }

        return response()->json(['success' => true]);
    }

    public function data()
    {
        $query = Liquidacion::query();

        return DataTables::of($query)
            ->addColumn('estado', function ($row) {
                return $row->estado == 1
                    ? '<span class="badge badge-success">Sin Entregar</span>'
                    : '<span class="badge badge-danger">Entregado</span>';
            })
            ->addColumn('acciones', function ($row) {
                $btnPdf = '<a href="' . route('admin.liquidaciones.pdf', $row->id) . '" target="_blank" class="btn btn-sm btn-danger" title="Imprimir PDF"><i class="fas fa-file-pdf"></i></a>';
                $btnVer = '<button class="btn btn-info btn-sm ver" data-id="' . $row->id . '" title="Ver Detalle"><i class="fas fa-eye"></i></button>';

                $btnEstado = '';
                if ($row->estado == '2') {
                    $btnEstado = '<button class="btn btn-danger btn-sm abrir" data-id="' . $row->id . '">Abrir</button>';
                } else {
                    $btnEstado = '<button class="btn btn-primary btn-sm cerrar" data-id="' . $row->id . '">Entregar</button>';
                }

                return $btnVer . ' ' . $btnPdf . ' ' . $btnEstado;
            })
            ->editColumn('asignado', fn($r) => 'L. ' . number_format($r->asignado, 2))
            ->editColumn('total', fn($r) => 'L. ' . number_format($r->total, 2))
            ->editColumn('deposito', fn($r) => 'L. ' . number_format($r->deposito, 2))
            ->rawColumns(['estado', 'acciones'])
            ->make(true);
    }


    public function show($id)
    {
        return Liquidacion::with('gastos')->findOrFail($id);
    }

    public function liquidar($id)
    {
        $liquidacion = Liquidacion::findOrFail($id);
        // Cambiar estado a 2 (Entregado)
        $liquidacion->estado = 2;
        $liquidacion->save();

        return response()->json([
            'status' => 'success',
            'message' => 'La liquidación ha sido marcada como entregada.'
        ]);
    }

    public function reabrir($id)
    {


        $liq = Liquidacion::findOrFail($id);
        $liq->estado = '1';
        $liq->save();
        return response()->json(['success' => true]);
    }


    public function liquidacion(Request $request)
    {
        $solicitud = null;

        if ($request->solicitud_id) {
            $solicitud = Solicitud::find($request->solicitud_id);
        }

        return view('admin.liquidaciones.liquidacion', compact('solicitud'));
    }



    public function generarPdf($id)
    {
        $liquidacion = Liquidacion::with('gastos')->findOrFail($id);

        // Suma total de los gastos reportados
        $totalGastos = $liquidacion->gastos->sum('monto');
        $montoAsignado = $liquidacion->asignado;

        // Diferencia: Positivo = Sobrante a devolver, Negativo = Excedente a reembolsar
        $diferencia = $montoAsignado - $totalGastos;

        if ($diferencia > 0) {
            $tipoBalance = 'DEVOLUCION'; // El empleado devuelve dinero a la empresa
            $montoBalance = $diferencia;
        } elseif ($diferencia < 0) {
            $tipoBalance = 'REEMBOLSO';  // La empresa reembolsa al empleado
            $montoBalance = abs($diferencia);
        } else {
            $tipoBalance = 'SALDADO';    // Gastó exactamente lo asignado
            $montoBalance = 0;
        }

        $pdf = Pdf::loadView('admin.liquidaciones.pdf', compact(
            'liquidacion',
            'totalGastos',
            'montoAsignado',
            'diferencia',
            'tipoBalance',
            'montoBalance'
        ));

        return $pdf->stream("liquidacion_{$liquidacion->id}.pdf");
    }

}
