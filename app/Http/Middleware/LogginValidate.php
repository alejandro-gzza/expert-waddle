<?php

namespace App\Http\Middleware;

use Closure;

class LogginValidate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        // Validar si el usuario tiene sesion

        if(empty(\Session::get('vc_user_system'))){ return redirect()-> to('/');}
        else { return $next($request); }

    }
}
