<?php

namespace App\Http\Controllers\Finanzas;

use App\Http\Controllers\Controller;
use App\Models\Ingreso;
use Illuminate\Http\Request;
use App\Models\Categoria;





class IngresoController extends Controller
{

    public function index()
    {
        $categorias = Categoria::where('tipo', 'ingreso')->get();
        return view('admin.finanzas.ingresos.index', compact('categorias'));
    }

    // DATATABLE
    public function data()
    {
        $ingresos = Ingreso::with('categoria')->get();

        return datatables()->of($ingresos)

            ->addColumn('categoria', function ($row) {
                return $row->categoria->nombre ?? 'Sin categoría';
            })

            ->addColumn('acciones', function ($row) {
                return '
            <button class="btn btn-warning btn-sm editar" data-id="' . $row->id . '">Editar</button>
            <button class="btn btn-danger btn-sm eliminar" data-id="' . $row->id . '">Eliminar</button>
            ';
            })

            ->rawColumns(['acciones'])
            ->make(true);
    }



    public function edit($id)
    {
        return Ingreso::find($id);
    }

    public function update(Request $request, $id)
    {
        $ingreso = Ingreso::find($id);
        $ingreso->update($request->all());

        return response()->json(['success' => 'ok']);
    }

    public function destroy($id)
    {
        Ingreso::find($id)->delete();

        return response()->json(['success' => 'ok']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        Ingreso::create([
            'categoria_id' => $request->categoria_id,
            'descripcion' => $request->descripcion,
            'monto' => $request->monto,
            'fecha' => $request->fecha,
            'metodo_pago' => $request->metodo
        ]);

        return back()->with('success', 'Ingreso registrado');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
}



