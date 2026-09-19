<?php
namespace App\Http\Controllers;

use App\Models\Credencial;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
class CredencialController extends Controller
{

    public function index()
    {
        $credenciales = Credencial::all();
        return view('admin.credenciales.index', compact('credenciales'));
    }

    public function create()
    {
        return view('admin.credenciales.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if($request->hasFile('foto')){
            $file = $request->file('foto');
            $nombre = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('fotos'),$nombre);
            $data['foto']=$nombre;
        }

        $data['codigo']=uniqid();

        Credencial::create($data);

        return redirect()->route('admin.credenciales.index');
    }

    public function edit($id)
    {

    // dd('edit de credenciales');
        $credencial = Credencial::findOrFail($id);
        return view('admin.credenciales.edit',compact('credencial'));
    }

    public function update(Request $request,$id)
    {
        $credencial = Credencial::findOrFail($id);

        $data=$request->all();

        if($request->hasFile('foto')){
            $file=$request->file('foto');
            $nombre=time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('fotos'),$nombre);
            $data['foto']=$nombre;
        }

        $credencial->update($data);

        return redirect()->route('admin.credenciales.index');
    }

    public function destroy($id)
    {
        Credencial::destroy($id);
        return redirect()->route('admin.credenciales.index');
    }

    public function imprimir($id)
    {
        $credencial = Credencial::findOrFail($id);

        $pdf = PDF::loadView('admin.credenciales.pdf',compact('credencial'))
        ->setPaper([0,0,242,153],'landscape');

        return $pdf->stream('credencial.pdf');
    }
}