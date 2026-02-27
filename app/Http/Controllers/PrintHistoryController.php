<?php

namespace App\Http\Controllers;

use App\Models\PrintHistory;

class PrintHistoryController extends Controller
{
    public function index(){
        $histories = PrintHistory::with('user','design')->latest()->get();
        return view('admin.idcards.history', compact('histories'));
    }
}
