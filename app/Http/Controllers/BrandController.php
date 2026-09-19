<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

// app/Http/Controllers/BrandController.php
class BrandController extends Controller
{
    // public function show(Brand $brand)
// {
//     $files = $brand->files()
//         ->orderBy('model')
//         ->get()
//         ->groupBy('type');

    //     $notes = $brand->notes()->latest()->get();


    //     return view('admin.brands.show', compact(
//         'brand',
//         'files',
//         'notes'
//     ));
// }

    public function index()
    {
        $brands = Brand::withCount('files')
            ->orderBy('name')
            ->get();

        // return $brands;
        return view('admin.brands.index', compact('brands'));
    }
    public function show(Brand $brand)
    {
        // Traer archivos y notas
        $files = $brand->files()->orderBy('model')->get()->groupBy('type');
        $notes = $brand->notes()->latest()->get();
        $brands = Brand::orderBy('name')->get();
        // return $brands;
        return view('admin.brands.show', compact('brand', 'files', 'notes', 'brands'));
    }


  public function store(Request $request)
{
    $request->validate([
        'name' => 'required|unique:brands,name',
        'logo_url' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'color_primary' => 'nullable|string',
    ]);

    $data = $request->only(['name', 'color_primary']);

    // Generar slug automáticamente
    $data['slug'] = Str::slug($request->name);

    // Si hay imagen
    if ($request->hasFile('logo_url')) {

        $image = $request->file('logo_url');

        // Nombre único
        $filename = time() . '_' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();

        // Guardar en storage/app/public/brands
        $image->storeAs('brands', $filename, 'public');

        // Guardar la URL en base de datos
        $data['logo_url'] = 'storage/brands/' . $filename;
    }

    Brand::create($data);

    return redirect()->route('admin.brands.index')
        ->with('success', 'Marca creada correctamente');
}

}




