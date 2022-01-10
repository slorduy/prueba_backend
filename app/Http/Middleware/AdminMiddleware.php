<?php

namespace App\Http\Middleware;
use App\User;
use Closure;
use JWTAuth;


class AdminMiddleware
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
        $user = JWTAuth::parseToken()->authenticate();
        if($user){
            return  $user->admin ?  $next($request) :  response()->json(['status'=>true,'Message' => 'Usuario no autorizado para esta accion'],401);
        }else{
            return response()->json(['status'=>true,'Message' => 'Usuario no encontrado'],400);
        }
    }
}
