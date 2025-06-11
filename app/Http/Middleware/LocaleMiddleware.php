<?php
// app/Http/Middleware/LocaleMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LocaleMiddleware
{

    public function handle($request, Closure $next) {
        
     // Verifica si hay 'locale' en la sesión
     if (Session::has('locale')) {
        App::setLocale(Session::get('locale'));
     }
    
        return $next($request);
    }


}