<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\Panel;
use App\Models\Port;
use Illuminate\Http\Request;

class PanelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
  $port = Connection::with('port','port.patch_panel')->get();
    //    return response()->json($port); 

if ($port->isNotEmpty()) {
    $patchPanelId = $port->first()->patch_panel_id;
    $PPA = Port::where('patch_panel_id', $patchPanelId)->get();
}
    //  return response()->json($PPA); 
     return view ('admin.paneles.index',compact('PPA'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Panel $panel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Panel $panel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Panel $panel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Panel $panel)
    {
        //
    }
}
