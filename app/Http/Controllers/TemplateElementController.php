<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TemplateElement;
use Illuminate\Http\Request;

class TemplateElementController extends Controller
{
    public function updatePosition(Request $request)
    {
        TemplateElement::where('id',$request->id)
            ->update([
                'x'=>$request->x,
                'y'=>$request->y
            ]);

        return response()->json(['ok'=>true]);
    }
}
