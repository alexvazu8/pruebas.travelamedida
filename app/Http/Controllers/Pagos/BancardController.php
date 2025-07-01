<?php

namespace App\Http\Controllers\Pagos;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Reserva;
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

                // Generar token
                $amount = number_format($request->amount, 2, '.', '');
               
            // $amount=$amount*100;
                
                //$amount=intval($amount);
                Log::debug('Formato Monto: ', ['amount' =>$amount]);
                Log::debug('Token antes del MD5: ', ['token_antes' =>$privateKey.$pago->id.$amount.'USD']);
                //$token = hash('sha256', $privateKey.$pago->id.$amount.'USD');
                $token = md5($privateKey.$pago->id.$amount.'USD');
                $amount = floatval($amount);
                Log::debug('Token generado para Bancard', ['token' => $token]);

                $payload = [
                    'public_key' => $publicKey,
                    'operation' => [
                        'token' => $token,
                        'shop_process_id' => $pago->id,
                        'currency' => 'USD',
                        'amount' => $amount,
                        'description' => "Pagos Bancard y Vision Mundo",
                        'return_url' => route('pagos.callback', $pago->id),
                        'cancel_url' => route('pagos.cancelar', $pago),
                        'additional_data' => [
                            '3ds' => [
                                'enabled' => true,
                                'challenge_indicator' => '02' // 02 = 3DS obligatorio
                            ]
                        ]
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
         $operation = $request->input('operation');
        // Opción 1: Si los datos llegan como texto plano (ej: "Array(...) {...}")
            $rawContent = $request->getContent();
            
            // Extrae el JSON manualmente (si existe)
            preg_match('/\{(.*?)\}/', $rawContent, $matches);
            $jsonData = $matches[0] ?? '{}';
            $decodedData = json_decode($jsonData, true); // Convierte JSON a array

           /* // Opción 2: Si los datos llegan como form-data o JSON estándar
            $requestData = $request->all(); // Usar esto si el request está bien formado

            // Log para depuración (verifica qué llega realmente)
            Log::debug('Raw Bancard Callback:', ['raw' => $rawContent, 'decoded' => $decodedData]);
            */
            // Procesa el estado de error
            if (($decodedData['success'] ?? null) === false || str_contains($rawContent, 'payment_fail')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $decodedData['error'] ?? 'Error desconocido en el pago',
                ], 400);
            }
        $shopProcessId = $operation['shop_process_id'] ?? null;

        if (!$shopProcessId) {
            return response()->json(['error' => 'ID de proceso no proporcionado'], 400);
        }

        $pago = Pago::find($shopProcessId);

        if (!$pago) {
            return response()->json(['error' => 'Pago no encontrado'], 404);
        }

        // Verificar autenticación 3DS
        if (($operation['security_information']['3d_secure'] ?? null) !== 'authenticated') {
            $pago->update(['estado' => 'fallido_3ds']);
            return response()->json(['error' => 'Falla en 3DS'], 400);
        }

        // Pago exitoso
        $pago->update([
            'estado' => 'PAGADO',
            'fecha_pago' => now(),
            'autorizacion' => $operation['authorization_number'] ?? null
        ]);

        return redirect()->route('reservas.confirmar');
    }


    // Nuevo método para verificar estado del pago
    public function verificarEstado($pagoId)
    {
        $pago = Pago::findOrFail($pagoId);
        
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
