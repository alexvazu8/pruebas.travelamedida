<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'api/auth/*',
        'pagos/callback', // 👈 Agrega esta línea exactamente como tu ruta

    ];
}
