<?php

// app/Http/Controllers/CurrencyController.php
namespace App\Http\Controllers;

use App\Models\TiposCambio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CurrencyController extends Controller
{
    private $simbolos = [
        'USD' => '$',
        'BOB' => 'Bs',
        'BRL' => 'R$',
        'PYG' => '₲',
        'USDT' => 'USDT '
    ];
    
    /**
     * Convertir monto USD a moneda actual
     */
    public function convertir(float $montoUSD): float
    {
        $moneda = Session::get('moneda', 'USD');
        
        if ($moneda === 'USD') {
            return $montoUSD;
        }
        
        $tasa = $this->obtenerTasa('USD', $moneda);
        return $montoUSD * $tasa;
    }
    
    /**
     * Formatear monto con símbolo
     */
    public function formatear(float $montoUSD): string
    {
        $moneda = Session::get('moneda', 'USD');
        $convertido = $this->convertir($montoUSD);
        $simbolo = $this->simbolos[$moneda] ?? $moneda . ' ';
        
        return $simbolo ." ". number_format($convertido, 2, '.', ',');
    }
    
    /**
     * Obtener tasa de cambio
     */
    function obtenerTasa(string $origen, string $destino): float
    {
        if ($origen === $destino) {
            return 1.0;
        }
        
        $tasa = TiposCambio::where('moneda_origen', $origen)
            ->where('moneda_destino', $destino)
            ->whereDate('fecha_validez', '>=', now())
            ->orderBy('fecha_validez', 'desc')
            ->first();
        
        if ($tasa) {
            return $tasa->tasa_cambio;
        }
        
        // Tasas por defecto si no hay en BD
        $defaultRates = [
            'USD_BOB' => 6.91,
            'USD_BRL' => 5.30,
            'USD_PYG' => 6800,
            'USD_USDT' => 1
        ];
        
        $key = "{$origen}_{$destino}";
        return $defaultRates[$key] ?? 1.0;
    }
    
    /**
     * Cambiar moneda (desde ruta/web)
     */
    public function cambiarMoneda(Request $request, $moneda)
    {
        $moneda = strtoupper($moneda);
        $monedasPermitidas = ['USD', 'BOB', 'BRL', 'PYG' ,'USDT'];
        
        if (!in_array($moneda, $monedasPermitidas)) {
            return back()->with('error', 'Moneda no válida');
        }
        
        Session::put('moneda', $moneda);
        
        return back()->with('success', "Moneda cambiada a {$moneda}");
    }
    
    /**
     * Obtener información de moneda actual
     */
    public function getMonedaInfo()
    {
        $moneda = Session::get('moneda', 'USD');
        
        return [
            'codigo' => $moneda,
            'simbolo' => $this->simbolos[$moneda] ?? '$',
            'tasa' => $this->obtenerTasa('USD', $moneda)
        ];
    }
    
    /**
     * Convertir y formatear para vistas
     */
    public function convertirYFormatear(float $montoUSD): array
    {
        return [
            'monto' => $this->convertir($montoUSD),
            'formateado' => $this->formatear($montoUSD),
            'moneda' => Session::get('moneda', 'USD')
        ];
    }
}
