<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Reserva;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\isEmpty;

class ReservaController extends Controller
{

/**
 * @OA\Post(
 *     path="/reservas/confirmar",
 *     summary="Confirmar una nueva reserva",
 *     description="Crea una nueva reserva con los datos proporcionados y asigna un localizador único.",
 *     tags={"Reservas"},
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
 *         description="Datos de la reserva",
 *         @OA\JsonContent(
 *             required={"guid","Localizador", "Importe_Reserva", "Nombre_Cliente", "Telefono_Cliente", "Email_contacto_reserva"},
 *             @OA\Property(property="guid", type="string", maxLength=36, example="88277835-350a-11f0-9c17-14cb19858a69", description="Identificador unico GUID"),
 *             @OA\Property(property="Localizador", type="string", maxLength=6, example="ABC123", description="Código único de localización"),
 *             @OA\Property(property="Importe_Reserva", type="number", format="float", minimum=0, example=150.75, description="Importe total de la reserva"),
 *             @OA\Property(property="Nombre_Cliente", type="string", maxLength=100, example="Juan"),
 *             @OA\Property(property="Apellido_Cliente", type="string", maxLength=100, example="Perez"),
 *             @OA\Property(property="Telefono_Cliente", type="string", maxLength=100, example="+34 600 123 456", pattern="^[\+\-0-9\s]+$"),
 *             @OA\Property(property="Email_contacto_reserva", type="string", format="email", maxLength=100, example="cliente@example.com"),
 *             @OA\Property(property="Comentarios", type="string", maxLength=500, nullable=true, example="Necesita habitación adaptada")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Reserva confirmada exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Reserva confirmada exitosamente"),
 *             @OA\Property(property="new_token", type="string", example="26|LVf1ouNSJvBNlz5p8g9EtzGAhxGyW98JeEnJ6smda135d21e"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="localizador", type="string", example="ABC123")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Error de validación",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Error de validación"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "Localizador": {"Este localizador ya está en uso"},
 *                     "Importe_Reserva": {"El importe no puede ser negativo"}
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Error al crear la reserva"),
 *             @OA\Property(property="error", type="string", example="Mensaje de error detallado")
 *         )
 *     )
 * )
 */

    public function confirmar(Request $request): JsonResponse
    {   
        // Validación manual (si no usas FormRequest)
        $validator = Validator::make($request->all(), [
            'guid'          => 'required|string|max:36|unique:reservas,guid',
            'Localizador'          => 'required|string|max:6|unique:reservas,Localizador',
            'Importe_Reserva'      => 'required|numeric|min:0',
            'Nombre_Cliente'       => 'required|string|max:100',
            'Apellido_Cliente'       => 'required|string|max:100',
            'Telefono_Cliente' => 'required|string|max:100|regex:/^[\+\-0-9\s]+$/',
            'Email_contacto_reserva' => 'required|email|max:100',
            'Comentarios'          => 'nullable|string|max:500',
        ], [
            'Localizador.required' => 'El localizador es obligatorio.',
            'Localizador.unique'   => 'Este localizador ya está en uso.',
            'Importe_Reserva.min'  => 'El importe no puede ser negativo.',
            'Email_contacto_reserva.email' => 'El email no es válido.',
        ]);
    
        // Si falla la validación, retorna errores en JSON
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors'  => $validator->errors(),
            ], 422); // Código HTTP 422: Unprocessable Entity
        }
    
        // Asignar el ID del usuario autenticado
        $request['Usuario_id'] = Auth::id();

    
        try {
              // Debe llegar un GUID único de 36 chars
            
            $reserva = Reserva::create($request->all());

             // Obtener el usuario autenticado directamente con auth()
            $user = Auth::user(); // o auth()->user()
            $user->tokens()->delete(); // Esto sí elimina sus tokens reales

            // Crear un nuevo token
            $newToken = $user->createToken('auth-token')->plainTextToken;
    
            return response()->json([
                'success' => true,
                'message' => 'Reserva confirmada exitosamente',
                'new_token' => $newToken,
                'data'    => [
                    'reserva'     => $reserva,
                    'localizador' => $reserva->Localizador,
                ],
            ], 201); // Código HTTP 201: Created
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la reserva',
                'error'   => $e->getMessage(),
            ], 500); // Código HTTP 500: Internal Server Error
        }
    }

/**
 * @OA\Get(
 *     path="/reserva/{loc}",
 *     summary="Obtener detalles de una reserva por localizador",
 *     description="Muestra los detalles completos de una reserva específica identificada por su localizador. Solo accesible por el usuario dueño de la reserva.",
 *     tags={"Reservas"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="loc",
 *         in="path",
 *         required=true,
 *         description="Localizador único de la reserva",
 *         @OA\Schema(
 *             type="string",
 *             example="X5XDZ"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Reserva obtenida exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Reserva obtenida exitosamente"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="reserva",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=16),
 *                     @OA\Property(property="Localizador", type="string", example="X5XDZ"),
 *                     @OA\Property(property="Importe_Reserva", type="number", format="float", example=44.4),
 *                     @OA\Property(property="Nombre_Cliente", type="string", example="Alejandro"),
 *                     @OA\Property(property="Apellido_Cliente", type="string", nullable=true, example="Rodriguez"),
 *                     @OA\Property(property="Telefono_Cliente", type="string", nullable=true),
 *                     @OA\Property(property="Email_contacto_reserva", type="string", format="email", example="alexvazu@gmail.com"),
 *                     @OA\Property(property="Comentarios", type="string", nullable=true),
 *                     @OA\Property(property="Usuario_id", type="integer", example=1),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-22T21:17:02.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-22T21:17:02.000000Z")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="No autorizado",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="No tienes permiso para ver esta reserva"),
 *             @OA\Property(property="code", type="integer", example=403)
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="No autenticado",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated")
 *         )
 *     )
 * )
 */
    public function showReserva($loc): JsonResponse
    {
      

        // Verificar permisos
        $datosLimpios['Usuario_id'] = Auth::id();
        $reserva = Reserva::where('Usuario_id', $datosLimpios['Usuario_id'])
                         ->where('Localizador', $loc)
                         ->first();

        if (!$reserva) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para ver esta reserva',
                'code' => 403
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reserva obtenida exitosamente',
            'data' => [
                'reserva' => $reserva
            ]
        ]);
    }

/**
 * @OA\Get(
 *     path="/listaReservas/{pagina}",
 *     summary="Lista de reservas paginadas",
 *     description="Obtiene un listado paginado de las reservas del usuario autenticado, ordenadas por fecha de creación descendente.",
 *     tags={"Reservas"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="pagina",
 *         in="path",
 *         required=true,
 *         description="Número de página a consultar",
 *         @OA\Schema(
 *             type="integer",
 *             minimum=1,
 *             example=1
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Listado de reservas obtenido exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Reservas obtenidas exitosamente"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(
 *                     property="data",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=16),
 *                         @OA\Property(property="Localizador", type="string", example="X5XDZ"),
 *                         @OA\Property(property="Importe_Reserva", type="number", format="float", example=44.4),
 *                         @OA\Property(property="Nombre_Cliente", type="string", example="Alejandro"),
 *                         @OA\Property(property="Apellido_Cliente", type="string", nullable=true, example="Rodriguez"),
 *                         @OA\Property(property="Telefono_Cliente", type="string", nullable=true),
 *                         @OA\Property(property="Email_contacto_reserva", type="string", format="email", example="alexvazu@gmail.com"),
 *                         @OA\Property(property="Comentarios", type="string", nullable=true),
 *                         @OA\Property(property="Usuario_id", type="integer", example=1),
 *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-22T21:17:02.000000Z"),
 *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-22T21:17:02.000000Z")
 *                     )
 *                 ),
 *                 @OA\Property(property="first_page_url", type="string", example="http://127.0.0.1:8000/api/auth/listaReservas/1?page=1"),
 *                 @OA\Property(property="from", type="integer", example=1),
 *                 @OA\Property(property="last_page", type="integer", example=1),
 *                 @OA\Property(property="last_page_url", type="string", example="http://127.0.0.1:8000/api/auth/listaReservas/1?page=1"),
 *                 @OA\Property(
 *                     property="links",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="url", type="string", nullable=true),
 *                         @OA\Property(property="label", type="string", example="1"),
 *                         @OA\Property(property="active", type="boolean", example=true)
 *                     )
 *                 ),
 *                 @OA\Property(property="next_page_url", type="string", nullable=true),
 *                 @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/auth/listaReservas/1"),
 *                 @OA\Property(property="per_page", type="integer", example=10),
 *                 @OA\Property(property="prev_page_url", type="string", nullable=true),
 *                 @OA\Property(property="to", type="integer", example=9),
 *                 @OA\Property(property="total", type="integer", example=9)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="No autorizado o sin resultados",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="No tienes permiso para ver estas reservas o no hay resultados"),
 *             @OA\Property(property="code", type="integer", example=403)
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="No autenticado",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated")
 *         )
 *     )
 * )
 */
    public function listaReservas($pagina): JsonResponse
    {
      

        // Verificar permisos
        $datosLimpios['Usuario_id'] = Auth::id();
        $reserva = Reserva::where('Usuario_id', $datosLimpios['Usuario_id'])
        ->orderBy('created_at', 'desc')                 
        ->paginate(10, ['*'], 'page', $pagina);

        if ($reserva->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para ver esta reserva o no hay resultado.',
                'code' => 403
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reserva obtenida exitosamente',
            'data' =>  $reserva
        ]);
    }

}