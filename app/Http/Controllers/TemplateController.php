<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\Template;
use App\Models\Person;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index()
    {
        return view('admin.templates.index',[
            'templates'=>Template::all()
        ]);
    }

    public function create()
    {
        return view('admin.templates.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'=>'required',
            'background'=>'nullable|image'
        ]);

        if($request->hasFile('background')){
            $data['background'] = $request->background->store('templates','public');
        }

        Template::create($data);

        return redirect()->route('templates.index');
    }

    public function show(Template $template)
    {
        return view('admin.templates.designer',compact('template'));
    }

    public function destroy(Template $template)
    {
        $template->delete();
        return back();
    }

    /* IMPRESIÓN */
  public function print(Request $request, Template $template)
{
    $request->validate([
        'ids' => 'required|array'
    ]);

    $empleados = Empleado::whereIn('reloj', $request->ids)->get();
// return $request;
    return view('admin.templates.print', compact('template','empleados'));
}

}
