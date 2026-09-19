<?php
namespace Modules\NetLan\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\NetLan\Entities\Departamento;
use Illuminate\Http\Request;



class DepartamentoController extends Controller
{
    public function index()
    {
        $departamentos =Departamento::all();
        // $departamento= Departamento::find($id);
        return view('netlan::admin.departamentos.index', compact('departamentos'));
    }

    public function create()
    {
        return view('admin.departamentos.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:255']);
       Departamento::create($request->all());

        return redirect()->route('admin.departamentos.index')->with('success', 'Departamento creado con éxito.');
    }

    public function show(Departamento $departamento)
    {
        return view('admin.departamentos.show', compact('departamento'));
    }

    public function edit( $id)
    {
        $departamento= Departamento::find($id);
        return view('admin.departamentos.edit', compact('departamento'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nombre' => 'required|string|max:255']);
        $departamento = Departamento::find($id);

        $departamento->update($request->all());

        return redirect()->route('admin.departamentos.index')->with('success', 'Departamento actualizado.');
    }

    public function destroy( $id)
    {
        $departamento = Departamento::find($id);
        $departamento->delete();

        return redirect()->route('admin.departamentos.index')
        ->with('mensaje', 'Departamento eliminado correctamente')
        ->with('icono', 'success');
    }
}
