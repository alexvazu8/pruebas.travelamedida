<?php

use App\Http\Middleware\CapturePostData;
use App\Http\Middleware\LocaleMiddleware;
use App\Http\Middleware\VerificarCabeceraReserva;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Middleware\ShareCurrency;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\VerifyPayment;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Limiter\Limit;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'guardaPost' => CapturePostData::class,
            'verifyPayment' => VerifyPayment::class,
            'cabecera.reserva' => VerificarCabeceraReserva::class,
            'rol' => RoleMiddleware::class,
        ]);
         // Agregar LocaleMiddleware al grupo 'web'
         $middleware->web( [ 
           // \Illuminate\Cookie\Middleware\EncryptCookies::class,
           // StartSession::class,
           VerifyCsrfToken::class,  
           LocaleMiddleware::class,
           ShareCurrency::class,
        ]);
        // Configuraci��n est��ndar para API
        $middleware->api([
          //  \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();