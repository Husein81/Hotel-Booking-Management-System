<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class CheckForLogin
{


    public function handle(Request $request,Closure $next):Response
    {
        if($request->url('admin/login') ) {
            if(isset(Auth::guard('admin')->user()->name)) {
                return redirect()->route('admins.dashboard');
            }
        }
        return $next($request);
    }
}


?>
