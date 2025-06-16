<?php


use App\Http\Controllers\Api\PagoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReservaController;
use App\Http\Controllers\Pagos\StereumPayController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/webhooks/stereum', [StereumPayController::class, 'webhook']);
// Grupo de autenticaci��n
Route::prefix('auth')->group(function () {
    // Autenticaci��n tradicional
    Route::post('/register', [AuthController::class, 'register']);   
    
    // Autenticaci��n con Google
    Route::post('/login/google', [AuthController::class, 'loginWithGoogle']);
    
    // Logout (protegido)
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    //Generar GUID para pagos
    Route::get('/pagos/generar-guid', [PagoController::class, 'generateUniqueGuid'])->middleware(['auth:sanctum', 'cabecera.reserva']);
    //Pagos pendientes
    Route::post('/pagos/crear-pendiente', [PagoController::class, 'crear_pago_pendiente'])->middleware(['auth:sanctum', 'cabecera.reserva']);
    // Verificar estado pagos
    Route::get('/pagos/verificar-estado', [PagoController::class, 'verificarEstadoPago'])->middleware(['auth:sanctum', 'cabecera.reserva']);
    // Confirmar Reserva (protegido)
    Route::post('/reserva/confirmar', [ReservaController::class, 'confirmar'])->middleware(['auth:sanctum', 'cabecera.reserva']);

    Route::get('/reserva/{loc}', [ReservaController::class, 'showReserva'])->middleware('auth:sanctum');
    Route::get('/listaReservas/{pagina}', [ReservaController::class, 'listaReservas'])->middleware('auth:sanctum');
});


/*// Rutas protegidas (ejemplo)
Route::middleware('auth:sanctum')->group(function () {
    //Route::get('/profile', [ProfileController::class, 'show']);
    // Otras rutas protegidas...
   
});*/