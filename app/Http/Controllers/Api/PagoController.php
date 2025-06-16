<?php

namespace App\Http\Controllers\Api;

use App\Models\Pago;
use Illuminate\Http\Request;
use App\Http\Requests\PagoRequest;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\PagoResource;
use App\Models\Reserva;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\Uuid;


class PagoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pagos = Pago::paginate();

        return PagoResource::collection($pagos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PagoRequest $request): Pago
    {
        return Pago::create($request->validated());
    }

/**
 * @OA\Post(
 *     path="/pagos/crear-pendiente",
 *     summary="Crear un pago pendiente",
 *     tags={"Pagos"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="X-Reserva-Segura",
 *         in="header",
 *         required=true,
 *         description="Cabecera de seguridad privada para uso interno",
 *         @OA\Schema(
 *             type="string",
 *             example="clave-super-secreta-123"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"token","expiration_token","usuario_id","monto","metodo_pago"},
 *             @OA\Property(property="guid", type="string", format="uuid", example="obtener-del-metodo-generar-guid"), 
 *             @OA\Property(property="token", type="string", example="AQUI-VA-EL-TOKEN-DE-VISION-MUNDO"),
 *             @OA\Property(property="expiration_token", type="integer", format="int64", example=1650326400),
 *             @OA\Property(property="usuario_id", type="integer", example=1),
 *             @OA\Property(property="monto", type="number", format="float", example=100.50),
 *             @OA\Property(property="transaction_id_metodo_pago", type="string", nullable=true, example="tx_123456"),
 *             @OA\Property(property="metodo_pago", type="string", enum={"StereumPay", "tarjeta"}, example="StereumPay"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Pago pendiente creado exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="guid", type="string", format="uuid"),
 *             @OA\Property(property="token", type="string"),
 *             @OA\Property(property="expiration_token", type="integer"),
 *             @OA\Property(property="usuario_id", type="integer"),
 *             @OA\Property(property="monto", type="number", format="float"),
 *             @OA\Property(property="transaction_id_metodo_pago", type="string", nullable=true),
 *             @OA\Property(property="metodo_pago", type="string"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Errores de validación",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "token": {"The token field is required."},
 *                     "monto": {"El monto debe ser al menos 2.01."}
 *                 }
 *             )
 *         )
 *     )
 * )
 */
    public function crear_pago_pendiente(Request $request): Pago
    {     
      
        $request->merge([
            'estado' => 'pendiente'
        ]);

    $messages = [
        'guid.required' => 'El campo guid es obligatorio.',
        'guid.string' => 'El guid debe ser una cadena de texto.',
        'guid.unique' => 'El guid ya existe en la base de datos.',
        'token.required' => 'El token es obligatorio.',
        'token.string' => 'El token debe ser una cadena de texto.',
        'expiration_token.required' => 'La expiración del token es obligatoria.',
        'expiration_token.integer' => 'La expiración del token debe ser un número entero.',
        'expiration_token.min' => 'La expiración del token no puede ser negativa.',
        'usuario_id.required' => 'El usuario es obligatorio.',
        'usuario_id.integer' => 'El usuario debe ser un número entero.',
        'usuario_id.exists' => 'El usuario no existe.',
        'monto.required' => 'El monto es obligatorio.',
        'monto.numeric' => 'El monto debe ser un número.',
        'monto.min' => 'El monto debe ser al menos 2.01.',
        'metodo_pago.required' => 'El método de pago es obligatorio.',
        'metodo_pago.string' => 'El método de pago debe ser una cadena de texto.',
        'metodo_pago.in' => 'El método de pago debe ser StereumPay o tarjeta.',
        'estado.required' => 'El estado es obligatorio.',
        'estado.string' => 'El estado debe ser una cadena de texto.',
        'estado.in' => 'El estado debe ser pendiente, pagado o cancelado.',
        'transaction_id_metodo_pago.required' => 'La transaction_id_metodo_pago es obligatoria.',
        'fecha_pago.date' => 'La fecha de pago debe ser una fecha válida.',
    ];

    $validated = $request->validate([
        'guid' => 'required|string|unique:pagos,guid',
        'token' => 'required|string',
        'expiration_token' => 'required|integer|min:0',
        'usuario_id' => 'required|integer|exists:users,id',
        'monto' => 'required|numeric|min:2.01',
        'metodo_pago' => 'required|string|in:StereumPay,tarjeta',
        'estado' => 'required|string|in:pendiente,pagado,cancelado',
        'transaction_id_metodo_pago' => 'required|string',
        'fecha_pago' => 'nullable|date',
    ], $messages);

    return Pago::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pago $pago): Pago
    {
        return $pago;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PagoRequest $request, Pago $pago): Pago
    {
        $pago->update($request->validated());

        return $pago;
    }

    public function destroy(Pago $pago): Response
    {
        $pago->delete();

        return response()->noContent();
    }

/**
 * @OA\Get(
 *     path="/pagos/generar-guid",
 *     summary="Generar un GUID único para pagos",
 *     tags={"Pagos"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="X-Reserva-Segura",
 *         in="header",
 *         required=true,
 *         description="Cabecera de seguridad privada para uso interno",
 *         @OA\Schema(
 *             type="string",
 *             example="clave-super-secreta-123"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="GUID generado correctamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="guid", type="string", example="550e8400-e29b-41d4-a716-446655440000")
 *         )
 *     )
 * )
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


        return response()->json([
        'guid' => $guid
        ]);
    }

    /**
 * @OA\Get(
 *     path="/pagos/verificar-estado",
 *     summary="Verificar el estado de un pago en PAGADO",
 *     tags={"Pagos"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="X-Reserva-Segura",
 *         in="header",
 *         required=true,
 *         description="Cabecera de seguridad privada para uso interno",
 *         @OA\Schema(
 *             type="string",
 *             example="clave-super-secreta-123"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="usuario_id",
 *         in="query",
 *         required=true,
 *         description="ID del usuario",
 *         @OA\Schema(type="integer", example=15)
 *     ),
 *     @OA\Parameter(
 *         name="transaction_id",
 *         in="query",
 *         required=true,
 *         description="ID de la transacción de pago Stereum",
 *         @OA\Schema(type="string", example="TRX123456789")
 *     ),
 *     @OA\Parameter(
 *         name="token",
 *         in="query",
 *         required=true,
 *         description="Token de Vision Mundo",
 *         @OA\Schema(type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUz...")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Estado del pago encontrado",
 *         @OA\JsonContent(
 *             @OA\Property(property="estado", type="string", example="pagado")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Pago no encontrado"
 *     )
 * )
 */
    public function verificarEstadoPago(Request $request)
    {
        $usuarioId = $request->query('usuario_id');
        $transactionId = $request->query('transaction_id');
        $tokenVM = $request->query('token');
        $now = Carbon::now()->timestamp;

        $pago = Pago::where('usuario_id', $usuarioId)
                    ->where('transaction_id_metodo_pago', $transactionId)
                    ->where('estado', 'PAGADO')
                    ->where('expiration_token', '>', $now)
                    ->where('token', $tokenVM)
                    ->latest()
                    ->first();

        if (!$pago) {
            return response()->json(['error' => 'Pago no encontrado'], 404);
        }

        return response()->json([
            'estado' => strtolower($pago->estado)
        ]);
    }
}
