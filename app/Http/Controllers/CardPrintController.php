<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\CardDesign;
use App\Models\PrintHistory;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Milon\Barcode\DNS1D;

class CardPrintController extends Controller
{
    public function printMass(Request $request)
    {
        $ids = json_decode($request->ids);
        $design = CardDesign::findOrFail($request->design_id);
        $personas = Persona::whereIn('id',$ids)->get();

        // Guardar historial
        PrintHistory::create([
            'user_id'=>auth()->id(),
            'card_design_id'=>$design->id,
            'total'=>count($ids),
            'person_ids'=>$ids,
            'type'=>'mass'
        ]);

        $pdf = Pdf::loadView('admin.idcards.print-mass', compact('personas','design'))
                  ->setPaper('letter','portrait');

        return $pdf->stream('carnets.pdf');
    }
}
