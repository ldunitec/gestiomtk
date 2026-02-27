<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use Illuminate\Http\Request;

class CiudadController extends Controller
{
    public function index()
    {
        return view('viaticos.ciudades.index');
    }

    public function list()
    {
        return response()->json(Ciudad::all());
    }

    public function store(Request $request)
    {
        Ciudad::create($request->all());
        return response()->json(['ok' => true]);
    }

    public function update(Request $request, $id)
    {
        Ciudad::findOrFail($id)->update($request->all());
        return response()->json(['ok' => true]);
    }

    public function destroy($id)
    {
        Ciudad::destroy($id);
        return response()->json(['ok' => true]);
    }
}

