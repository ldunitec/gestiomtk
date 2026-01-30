<?php

// app/Http/Controllers/NoteController.php
// app/Http/Controllers/NoteController.php
namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

// app/Http/Controllers/NoteController.php
class NoteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'nota'     => 'required'
        ]);

        Note::create($request->only('brand_id', 'titulo', 'nota'));

        return response()->json(['ok' => true]);
    }
}



