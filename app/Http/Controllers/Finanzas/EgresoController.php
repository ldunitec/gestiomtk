<?php

namespace App\Http\Controllers\Finanzas;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Egreso;
use Illuminate\Http\Request;

class EgresoController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $categorias = Categoria::where('tipo', 'egreso')->orderBy('nombre', 'asc')->get();
        return view('admin.finanzas.egresos.index', compact('categorias'));
    }

    // DATATABLE
    public function data()
    {
        $egresos = Egreso::with('categoria')->get();

        return datatables()->of($egresos)

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
        return Egreso::find($id);
    }

    public function update(Request $request, $id)
    {
        $egreso = Egreso::find($id);
        $egreso->update($request->all());

        return response()->json(['success' => 'ok']);
    }

    public function destroy($id)
    {
        Egreso::find($id)->delete();

        return response()->json(['success' => 'ok']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        Egreso::create([
            'categoria_id' => $request->categoria_id,
            'descripcion' => $request->descripcion,
            'monto' => $request->monto,
            'fecha' => $request->fecha,
            'metodo_pago' => $request->metodo
        ]);

        return back()->with('success', 'egreso registrado');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
}
