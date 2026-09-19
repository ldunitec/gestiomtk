<?php

namespace App\Http\Controllers;

use App\Models\Administrativo;
use App\Models\Agenda;
use App\Models\Asistencia;
use App\Models\Empleado;
use App\Models\InventarioBTR;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index()
    {
    
        $total_roles = Role::count();
        $total_permisos = Permission::count();
        $total_asistencia = Asistencia::count();
        // $total_empleados = Empleado::count();
        // $total_agenda = Agenda::where('estado','activo')->count();
        $total_btr = InventarioBTR::count();
        $total_ticket = Ticket::count();
    // dd($total_agenda);
        return view('admin.index',compact('total_roles','total_permisos','total_asistencia','total_btr','total_ticket'));
    }
}
