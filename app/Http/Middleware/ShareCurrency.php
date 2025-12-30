<?php
// app/Http/Middleware/ShareCurrency.php
namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\CurrencyController;

class ShareCurrency
{
    public function handle($request, Closure $next)
    {
        // Compartir instancia del controlador con todas las vistas
        view()->share('currency', app(CurrencyController::class));
        
        return $next($request);
    }
}