<?php

namespace App\Http\Middleware;

use App\Models\Pago;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class VerifyPayment
{
    public function handle(Request $request, Closure $next)
    {  $tokenVM = Cache::get('api_access_token');
        $now = Carbon::now()->timestamp;
        $pago = Pago::where('usuario_id', Auth::id())
                    ->where('estado', 'PAGADO')
                    ->where('expiration_token', '>', $now) // Solo tokens no expirados
                    ->where('token', $tokenVM)
                    ->latest()
                    ->first();
       
        if (!$pago) {
            return redirect()->route('carritos.show')
                ->with('error', 'Debes completar el pago antes de confirmar la reserva')
                ->withInput();

        }
        
        return $next($request);
    }
}