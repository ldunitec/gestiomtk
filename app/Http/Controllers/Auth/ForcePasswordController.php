<?php


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ForcePasswordController extends Controller
{
    
    public function show()
    {
        return view('auth.force-password');
    }

public function update(Request $request)
{
    $request->validate([
        'password' => 'required|confirmed|min:8',
    ]);

    // USAR DB para evitar problemas de Eloquent / GenericUser
    \DB::table('users')
        ->where('id', auth()->id())
        ->update([
            'password' => \Hash::make($request->password),
            'force_password_change' => false,
            'updated_at' => now(),
        ]);

    return redirect()->route('home');
}

}
