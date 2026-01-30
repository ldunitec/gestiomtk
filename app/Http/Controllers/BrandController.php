<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

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
        // Validamos los campos
        $request->validate([
            'name' => 'required|unique:brands,name',
            'logo_url' => 'nullable|url',
            'color_primary' => 'nullable|string',
        ]);

        // Creamos el slug a partir del name automáticamente
        $data = $request->all();
        $data['slug'] = strtolower(str_replace(' ', '-', $request->name)); // minuscula y espacios por guiones

        Brand::create($data);

        return redirect()->route('admin.brands.index')->with('success', 'Marca creada correctamente');
    }


}




