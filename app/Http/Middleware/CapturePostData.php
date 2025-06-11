<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CapturePostData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {   
        // Verifica si la solicitud es un POST
        if ($request->isMethod('post')) {
            // Guarda los datos del POST en la sesión
            session(['post_data' => $request->except('_token')]);
        }
        $postData = session('post_data');
        //dd($postData);

        return $next($request);
    }
}
