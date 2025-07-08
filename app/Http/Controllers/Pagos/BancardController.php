<?php

namespace App\Http\Controllers\Pagos;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Reserva;
use App\Models\TiposCambio;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BancardController extends Controller
{
    protected function generateUniqueGuid(): string
    {
        do {
            // 1) Genera un UUID v4 (e.g. "550e8400-e29b-41d4-a716-446655440000")
            $guid = Uuid::uuid4()->toString();

            // 2) Comprueba existencia en la BD
            $exists = Reserva::where('guid', $guid)->exists();

        // 3) Si existe, repite el bucle; si no, sale y devuelve $guid
        } while ($exists);

        return $guid;
    }

     // Paso 1: Iniciar el pago
    public function iniciarPago(Request $request)
    {
        Log::info('Iniciando proceso de pago', ['request' => $request->all()]);
        
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        return DB::transaction(function () use ($request) {
            try {
                Log::debug('Iniciando transacción de base de datos');

                // Generar GUID y obtener token de VM
                $guid = $this->generateUniqueGuid();
                $tokenVM = Cache::get('api_access_token');
                $expiration_tokenVM = Cache::get('api_access_token_expire_at');
                
                Log::debug('Datos obtenidos de Cache', [
                    'tokenVM' => $tokenVM ? 'existe' : 'no existe',
                    'expiration_tokenVM' => $expiration_tokenVM
                ]);


                // Crear registro de pago
                $pago = Pago::create([
                    'monto' => $request->amount,
                    'guid'  => $guid,
                    'token' => $tokenVM,
                    'expiration_token' => $expiration_tokenVM ? $expiration_tokenVM->timestamp : null,
                    'metodo_pago' => 'BancarVPOS',
                    'usuario_id' => Auth::id(),
                    'estado' => 'pendiente',
                ]);

                Log::info('Pago creado en DB', ['pago_id' => $pago->id]);

                // Configuración Bancard
                $privateKey = env('BANCARD_PRIVATE_KEY');
                $publicKey = env('BANCARD_PUBLIC_KEY');
                $apiUrl = env('BANCARD_BASE_URL');

                Log::debug('Variables de entorno Bancard', [
                    'privateKey' => $privateKey ? 'existe' : 'no existe',
                    'publicKey' => $publicKey ? 'existe' : 'no existe',
                    'apiUrl' => $apiUrl
                ]);

                               // Obtener el último tipo de cambio registrado para USD → PYG
                $ultimoTipoCambio = TiposCambio::where('moneda_origen', 'USD')
                    ->where('moneda_destino', 'PYG')
                    ->latest('fecha_validez') // Ordena por fecha más reciente
                    ->first();
                    
                //monto en Guaranies PYG
                
                

                // Generar token
                $amount = number_format($request->amount, 2, '.', '');
                $amount=$amount*$ultimoTipoCambio->tasa_cambio;
 
                
                //$amount=intval($amount);
                Log::debug('Formato Monto: ', ['amount' =>$amount]);
                Log::debug('Token antes del MD5: ', ['token_antes' =>$privateKey.$pago->id.$amount.'USD']);
                //$token = hash('sha256', $privateKey.$pago->id.$amount.'USD');
                $token = md5($privateKey.$pago->id.$amount.'PYG');
               
                Log::debug('Token generado para Bancard', ['token' => $token]);

                $payload = [
                    'public_key' => $publicKey,
                    'operation' => [
                        'token' => $token,
                        'shop_process_id' => $pago->id,
                        'currency' => 'PYG',
                        'amount' => $amount,
                        'description' => "Pagos Bancard y Vision Mundo",
                        'return_url' => route('carritos.show',['id_pago' => $pago->id]),
                        'cancel_url' => route('pagos.cancelar', $pago)
                    ]
                ];

                Log::debug('Payload para Bancard', $payload);

                $response = Http::post($apiUrl."/vpos/api/0.3/single_buy", $payload);
                $responseData = $response->json();

                Log::debug('Respuesta de Bancard', [
                    'status_code' => $response->status(),
                    'response' => $responseData
                ]);

                if (!$response->successful() ) {
                    Log::error('Error en respuesta de Bancard', [
                        'status' => $response->status(),
                        'response' => $responseData,
                        'successfull'=> $response->successful()

                    ]);
                    throw new \Exception('Error al iniciar pago con Bancard');
                }

                // Actualizar pago con datos de respuesta
                $pago->update([
                    'transaction_id_metodo_pago' => $responseData['process_id'] ?? null,
                ]);

                Log::info('Pago actualizado con transaction_id', [
                    'pago_id' => $pago->id,
                    'transaction_id' => $responseData['process_id']
                ]);

                return response()->json([
                    'status' => 'success',
                    'process_id' => $responseData['process_id'],
                    'pago_id' => $pago->id
                ]);

            } catch (\Exception $e) {
                Log::error('Error en iniciarPago', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e; // Relanza la excepción para mantener el comportamiento original
            }
        });
    }
    public function handleCallback(Request $request) {
         $data = $request->all();
        $operation = $data['operation'];
           
        // 1. Validar el token (seguridad crítica)
        $validToken = md5(env('BANCARD_PRIVATE_KEY') . $operation['shop_process_id'] ."confirm". $operation['amount']. $operation['currency']);
        if ($operation['token'] !== $validToken) {
            Log::error('Token inválido', ['received' => $operation['token'], 'expected' => $validToken]);
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // 2. Buscar el pago por shop_process_id (no por id_pago)
        
        $pago = Pago::find($operation['shop_process_id']);
        if (!$pago) {
            Log::error('Pago no encontrado', ['shop_process_id' => $operation['shop_process_id']]);
            return response()->json(['error' => 'Pago no existe'], 404);
        }
        //  Determinar el estado real (considerando response_code)
        $estado = $this->determinarEstado($operation['response'], $operation['response_code']);
        // 3. Actualizar el pago con todos los datos de Bancard
        $pago-> update([
            'estado' => $estado,
            'fecha_pago' => now(),
            'autorizacion' => $operation['authorization_number'] ?? null,            
            // Agrega otros campos si son necesarios
        ]);

        // 4. Registrar éxito
        Log::info('Pago actualizado', [
            'pago_id' => $pago->id,
            'nuevo_estado' => $pago->estado
        ]);

        return response()->json(['status' => 'success'], 200);
        
        

   
    }

    // Método auxiliar para determinar el estado
    private function determinarEstado($response, $responseCode)
    {
        if ($response === 'S') {
            return ($responseCode == '00') ? 'PAGADO' : 'DUPLICADO'; // Código 00 = éxito real
        }
        return 'RECHAZADO'; // Para response = "F"
    }


    // Nuevo método para verificar estado del pago
    public function verificarEstado($tokenVM)
    {
        $pago = Pago::where('token', $tokenVM)
            ->where('estado', 'PAGADO') // Asegurarse de que esté pagado
            ->first(); // Devuelve un solo modelo o `null`
        
        return response()->json([
            'status' => $pago->estado,
            'autorizacion' => $pago->autorizacion
        ]);
    }

    public function cancelar(Pago $pago)
    {
        // Verificar que el pago pertenece al usuario actual (seguridad)
        if ($pago->usuario_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No autorizado',
                'code' => 403
            ], 403);
        }

        // Actualizar estado del pago
        $pago->update([
            'estado' => 'cancelado',
           
        ]);

        // Respuesta JSON estructurada
        return response()->json([
            'status' => 'success',
            'message' => 'Pago cancelado exitosamente',
            'data' => [
                'pago_id' => $pago->id,
                'estado' => $pago->estado,
                'fecha_cancelacion' => now(),
                'monto' => $pago->monto,
                'metodo_pago' => $pago->metodo_pago
            ]
        ]);
    }

}
