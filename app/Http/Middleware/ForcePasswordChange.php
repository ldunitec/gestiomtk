<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
public function handle($request, Closure $next)
{
    if (
        auth()->check() &&
        auth()->user()->force_password_change &&
        ! $request->routeIs('password.change', 'password.update', 'logout')
    ) {
        return redirect()->route('password.change');
    }

    return $next($request);
}



// public function handle(Request $request, Closure $next)
// {
//     // ⛔ dejar pasar login SIEMPRE
//     if ($request->routeIs('login', 'login.post')) {
//         return $next($request);
//     }

//     if (
//         auth()->check() &&
//         auth()->user()->force_password_change &&
//         ! $request->routeIs(
//             'password.force',
//             'password.force.update',
//             'logout'
//         )
//     ) {
//         return redirect()->route('password.force');
//     }

//     return $next($request);
// }


// public function handle($request, Closure $next)
// {
//     if (
//         auth()->check() &&
//         auth()->user()->force_password_change &&
//         ! $request->routeIs('password.change', 'password.update', 'logout')
//     ) {
//         return redirect()->route('password.change');
//     }

//     return $next($request);
// }

    /**
     * Handle an incoming request.
 
 
    *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   
}
