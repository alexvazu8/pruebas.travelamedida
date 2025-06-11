<?php

namespace App\Http\Controllers\Pagos;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Reserva;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class StereumPayController extends Controller
{
    protected $apiUrl;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->apiUrl = config('services.stereumpay.base_url'); // Define esto en config/services.php
        $this->username = config('services.stereumpay.username');
        $this->password = config('services.stereumpay.password');
    }

        /**
     * Genera un UUID v4 y se asegura de que no exista en la tabla reservas.
     *
     * @return string
     */
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
    

    public function login()
    {
        try {
            $response = Http::post("{$this->apiUrl}/api/v1/auth/token?grant_type=password", [
                'username' => $this->username,
                'password' => $this->password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'] ?? null;
                $refresh = $data['refresh_token'] ?? null;
                $USER_ID = $data['user']['id'] ?? null;

                if ($token) {
                    // Puedes guardar el token en sesión o en cache, lo guardo en 240 minutos o sea 4 horas.
                      Cache::put('stereumpay_token', $token, now()->addMinutes(240));

                    return response()->json(['message' => 'Login exitoso', 'token' => $token]);
                } else {
                    return response()->json(['error' => 'Token no recibido'], 422);
                }
            }

            return response()->json(['error' => 'Credenciales inválidas'], 401);
        } catch (\Exception $e) {
            Log::error('Error en login StereumPay: ' . $e->getMessage());
            return response()->json(['error' => 'Error en la conexión con la API'], 500);
        }
    }

    public function createCharge(Request $request)
    {
        try {
            //print_r($request->all());
            $amount = $request->input('amount');
            //print_r($amount);
            $usuarioId = Auth::id();
            $cacheKey = "stereumpay_qr_{$usuarioId}";

            if (Cache::has($cacheKey)) {
                 $datos = Cache::get($cacheKey);
                 if(isset($datos['amount']) && ($datos['amount']==$amount)&&($datos['tokenVM']==Cache::get('api_access_token'))){
                    return response()->json([
                        'message' => 'QR existente',
                        'data' => Cache::get($cacheKey)
                    ]);
                 }  
            }            
            $token = Cache::get('stereumpay_token');

            if (!$token) {
                //return response()->json(['error' => 'Token no disponible. Inicia sesión primero.'], 401);
                $this->login();
                $token = Cache::get('stereumpay_token');
            }
            
            $payload = [
                'country'          => 'BO',
                'amount'           => $amount,
                'network'          => 'POLYGON',
                'currency'         => 'USDT',
                'idempotency_key'  => $guid=$this->generateUniqueGuid(), // Genera GUID único
                'charge_reason'    => 'Cobro Travelamedida',
                'callback'         => 'https://', // Cambiar por tu URL real
            ];

            $response = Http::withToken($token)
                ->withHeaders([
                    'x-api-key' => env('STEREUM_API_KEY'),
                ])
                ->post("{$this->apiUrl}/api/v1/transactions/create-charge", $payload);

            $tokenVM = Cache::get('api_access_token');
            $expiration_tokenVM = Cache::get('api_access_token_expire_at');
            $timestamp = $expiration_tokenVM->timestamp;
            //echo $fecha = Carbon::createFromTimestamp($timestamp);//fecha que vence.
            if ($response->successful()) {
                $respuestaApi = $response->json();
                $respuestaApi['tokenVM'] = $tokenVM;
                // Guarda en caché por 10 minutos
                Cache::put($cacheKey, $respuestaApi, now()->addMinutes(10));

                $datosPago = [
                'monto'           => $amount,
                'guid'  => $guid, // Genera GUID único
                'token' => $tokenVM, // es el de VM
                'expiration_token' => $timestamp,
                'metodo_pago' => 'StereumPay',
                'usuario_id'  => Auth::id() , // Id de usuario.
                'estado'         => 'pendiente', // Cambiar por cuando el estado este
                'transaction_id_metodo_pago'       => $respuestaApi['id'], // <- ID de la API
                ];
                //print_r($payload);
                //print_r($datosPago);
                Log::info('Datos del pago:', $datosPago);
                $pago = Pago::create($datosPago);

                return response()->json([
                    'message' => 'Cargo creado con éxito',
                    'data' => $response->json()
                ]);
            }

            return response()->json([
                'error' => 'Error al crear el cargo',
                'details' => $response->json()
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Error al crear cargo StereumPay: ' . $e->getMessage());
            return response()->json(['error' => 'Error en la conexión con la API' . $e->getMessage()], 500);
        }
    }
    public function checkPaymentStatus($transactionId)
    {
        $tokenVM = Cache::get('api_access_token');
            $now = Carbon::now()->timestamp;
            $pago = Pago::where('usuario_id', Auth::id())
                        ->where('transaction_id_metodo_pago', $transactionId)
                        ->where('estado', 'PAGADO')
                        ->where('expiration_token', '>', $now) // Solo tokens no expirados
                        ->where('token', $tokenVM)
                        ->latest()
                        ->first();
        if (!$pago) {
            return response()->json(['error' => 'Pago no encontrado'], 404);
        }

        return response()->json([
            'estado' => strtolower($pago->estado) // ejemplo: 'PENDIENTE', 'PAGADO'
        ]);
    }

    public function webhook(Request $request)
    {  
         $apiKey = env('STEREUM_API_KEY'); 

        $payload = $request->getContent(); // cuerpo crudo del JSON
         $firmaHeader = $request->header('x-signature'); 
        $timestampHeader = $request->header('x-timestamp');

        // Log opcional en desarrollo
        Log::info('📩 Webhook recibido de Stereum', [
            'firma' => $firmaHeader,
            'timestamp' => $timestampHeader,
            'body' => $payload
        ]);

        // Verifica existencia de headers críticos
        if (!$firmaHeader || !$timestampHeader) {
            Log::warning('🚫 Headers faltantes en webhook.');
            return response()->json(['error' => 'Headers faltantes'], 400);
        }
        
        //tiempo del server de stereum
        date_default_timezone_set('America/La_Paz');
        // Validar timestamp para evitar reenvíos viejos (5 min máx)
       echo $timestamp = (int) $timestampHeader;
       
       //al timestamp le aumento 14400 porque son las 4 horas de diferencia con el servidor de Bolivia de stereum aparentemente
        if (abs(time() - ($timestamp+14400)) > 300) {
            Log::warning('⏰ Timestamp fuera de rango', ['timestamp' => $timestamp+14400,'time'=>time()]);
            return response()->json(['error' => 'Timestamp inválido'], 400);
        }

        // Calcular firma y comparar
        $firmaCalculada = hash_hmac('sha256', $payload, $apiKey);
        if (!hash_equals($firmaCalculada, $firmaHeader)) {
            Log::warning('❌ Firma inválida', ['esperada' => $firmaCalculada, 'recibida' => $firmaHeader]);
            return response()->json(['error' => 'Firma no válida'], 401);
        }

        // Decodifica el JSON
        $data = json_decode($payload, true);
        if(isset($data['notification_type']) && $data['notification_type'] === 'test')
            { //pruebas de validacion de cualquier texto json
                return response()->json(['success' => true],200);
            }
        // Verifica y procesa si es transacción válida
        if (
            isset($data['notification_type']) &&
            $data['notification_type'] === 'transaction' &&
            isset($data['transaction']['idempotency_key']) &&
            isset($data['transaction']['status'])
        ) {
            $guid = $data['transaction']['idempotency_key'];
            $status = strtolower($data['transaction']['status']);

            $pago = Pago::where('guid', $guid)->first();

            if ($pago) {
                $pago->estado = $status;
                $pago->fecha_pago = now(); // Establece la fecha y hora actuales
                $pago->save();

                Log::info("✅ Estado de pago actualizado a '{$status}' para guid: {$guid}");

                return response()->json(['success' => true],200);
            }

            Log::warning("⚠️ Pago con guid {$guid} no encontrado.");
        }

        return response()->json(['success' => false],400);
    }

}