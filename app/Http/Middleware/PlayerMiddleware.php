<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PlayerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || (!$request->user()->isPlayer() && !$request->user()->isAdmin())) {
            return response()->json([
                'message' => 'Acceso denegado. Se requieren permisos de jugador o administrador.'
            ], 403);
        }

        return $next($request);
    }
}
