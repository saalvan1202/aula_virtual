<?php

namespace App\Http\Middleware;

use App\Models\Dispositivo;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class ExpiredSessionLogout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
         $user = Auth::user();
         if($user){
                $dispositivoExistente = Dispositivo::where('id_usuario', $user->id)
            ->where('ip', $request->ip())
            ->where('estado', 'A')
            ->exists();
            if($user->updated_at && $user->updated_at->diffInMinutes(now()) >= 1){
                    $user->session_id = null;  // Limpiar session_id
                    $user->save();
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect('/login')->with('message', 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.');
                }
         }
         return $next($request);
}
}
