<?php

namespace App\Http\Controllers;

use App\Models\Hotspot;
use Illuminate\Http\Request;

class HotspotController extends Controller
{
    public function login(Request $request)
    {
        return view('admin.hotspot.login', $request->all());
    }

    public function auth(Request $request)
    {
        $cliente = Cliente::where('mac_address', $request->username)->first();

        if (!$cliente) {
            abort(403, 'Cliente no registrado');
        }

        $alDia = Pago::where('cliente_id', $cliente->id)
            ->where('mes', now()->month)
            ->where('anio', now()->year)
            ->where('pagado', true)
            ->exists();

        if (!$alDia) {
            return view('hotspot.mora');
        }

        $password = 'internet'; // NO importa, MikroTik valida CHAP

        $chapPassword = md5(
            chr($request->chap_id) .
            $password .
            hex2bin($request->chap_challenge)
        );

        return redirect(
            $request->link_login .
            "?username={$request->username}&password={$chapPassword}"
        );
    }
}
