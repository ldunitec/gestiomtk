<?php

namespace App\Http\Controllers;

use App\Models\Administrativo;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index()
    {
    
        $total_roles = Role::count();
        $total_administrativo = Administrativo::count();
    
        return view('admin.index',compact('total_roles','total_administrativo'));
    }
}
