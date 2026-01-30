<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DeviceModel;
use Illuminate\Http\Request;

class DeviceModelController extends Controller
{
public function storeAjax(Request $request)
{
    $request->validate([
        'brand_id' => 'required',
        'category_id' => 'required',
        'name' => 'required'
    ]);

    $model = DeviceModel::create($request->all());

    return response()->json(
        $model->load('brand', 'category')
    );
}
}
