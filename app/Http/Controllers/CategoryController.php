<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
   public function storeAjax(Request $request)
{
    $request->validate([
        'name' => 'required|unique:categories,name'
    ]);

    return response()->json(
        Category::create(['name' => $request->name])
    );
}

}
