<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarCabeceraReserva
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
         $cabecera = $request->header('X-Reserva-Segura');

        // Comparar con un valor del .env
        if ($cabecera !== env('CLAVE_SEGURA_RESERVA')) {
            return response()->json(['error' => 'Cabecera de seguridad inválida'], 403);
        }

        return $next($request);
    }
}
