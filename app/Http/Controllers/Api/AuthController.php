<?php
//Este archivo es para generar las apis de Travel a Medida, las de login.
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

/**
 * @OA\Info(
 *     title="API de Reservas Travelamedida",
 *     version="1.0.0",
 *     description="Documentación de la API para el sistema de reservas Travelamedida",
 *     @OA\Contact(
 *         email="alexvazu@gmail.com",
 *         name="Equipo de Desarrollo"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Servidor API Principal"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     name="Authorization",
 *     in="header",
 *     description="JWT token, formato: Bearer {token}"
 * )
 */
class AuthController extends Controller
{
/**
 * @OA\Post(
 *     path="/register",
 *     summary="Registro de nuevo usuario",
 *     description="Crea una nueva cuenta de usuario y devuelve un token JWT para autenticación inmediata.",
 *     tags={"Autenticación"},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Datos de registro",
 *         @OA\JsonContent(
 *             required={"name","email","password","password_confirmation"},
 *             @OA\Property(property="name", type="string", maxLength=255, example="Juan Pérez"),
 *             @OA\Property(property="email", type="string", format="email", maxLength=255, example="usuario@example.com"),
 *             @OA\Property(
 *                 property="password", 
 *                 type="string", 
 *                 format="password", 
 *                 minLength=8,
 *                 example="Password123!",
 *                 description="Mínimo 8 caracteres, debe contener letras y números"
 *             ),
 *             @OA\Property(
 *                 property="password_confirmation", 
 *                 type="string", 
 *                 format="password", 
 *                 example="Password123!",
 *                 description="Debe coincidir con el campo password"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Registro exitoso",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Usuario registrado correctamente"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validación fallida",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Los datos proporcionados no son válidos"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "email": {"El correo ya está registrado"},
 *                     "password": {"La contraseña debe tener al menos 8 caracteres"}
 *                 }
 *             )
 *         )
 *     )
 * )
 */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $this->generateTokenResponse($user, 'Usuario registrado correctamente');
    }

    /**
 * @OA\Post(
 *     path="/login",
 *     summary="Autenticación de usuario",
 *     description="Inicia sesión con credenciales de email y contraseña. Devuelve un token JWT.",
 *     tags={"Autenticación"},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Credenciales de acceso",
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email", example="usuario@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123", minLength=8)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login exitoso",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Login exitoso"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Credenciales inválidas",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Credenciales incorrectas")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validación fallida",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Los datos proporcionados no son válidos"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "email": {"El campo email es obligatorio"},
 *                     "password": {"El campo contraseña es obligatorio"}
 *                 }
 *             )
 *         )
 *     )
 * )
 */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        return $this->generateTokenResponse(Auth::user(), 'Login exitoso');
    }

 /**
 * @OA\Post(
 *     path="/google",
 *     summary="Autenticación con Google",
 *     description="Autentica un usuario usando un token de acceso de Google OAuth. Crea el usuario si no existe.",
 *     tags={"Autenticación"},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Token de acceso de Google",
 *         @OA\JsonContent(
 *             required={"access_token"},
 *             @OA\Property(
 *                 property="access_token",
 *                 type="string",
 *                 example="ya29.a0AfB_by...",
 *                 description="Token de acceso OAuth 2.0 obtenido del cliente de Google"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Autenticación exitosa",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Autenticación con Google exitosa"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Token inválido o error de autenticación",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Error en autenticación con Google"),
 *             @OA\Property(property="error", type="string", example="Invalid token")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validación fallida",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Los datos proporcionados no son válidos"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "access_token": {"El campo access_token es obligatorio"}
 *                 }
 *             )
 *         )
 *     )
 * )
 */
    public function loginWithGoogle(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string'
        ]);

        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->userFromToken($request->access_token);

            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'password' => Hash::make(Str::random(16)),
                    'email_verified_at' => now(),
                    'remember_token' => $request->access_token,
                    'avatar' => $googleUser->getAvatar(),
                ]
            );

            return $this->generateTokenResponse($user, 'Autenticación con Google exitosa');

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error en autenticación con Google',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    // Método para generar tokens (reutilizable)
    protected function generateTokenResponse(User $user, string $message)
    {
        $token = $user->createToken('auth-token')->plainTextToken;
        
        return response()->json([
            'message' => $message,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar ?? null
            ],
            'token' => $token
        ]);
    }

    /**
 * @OA\Post(
 *     path="/logout",
 *     summary="Cerrar sesión",
 *     description="Invalida el token JWT actual del usuario, terminando la sesión activa.",
 *     tags={"Autenticación"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Sesión cerrada exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Sesión cerrada correctamente")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="No autorizado",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated")
 *         )
 *     )
 * )
 */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}