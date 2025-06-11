<?php
// Esta clase consume las apis de VISION MUNDO
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Realiza el login y guarda el token en caché.
     */
    public function authenticate()
    {   
        // Recuperar las credenciales y URL de la API desde el archivo .env
        $email = env('API_VM_USERNAME');
        $password = env('API_VM_PASSWORD');
        $apiUrl = env('API_VM_URL') . '/login';  // URL de login, ajusta si es necesario
       
        // Hacer la solicitud para obtener el token
        $response = Http::post($apiUrl, [
            'email' => $email,
            'password' => $password,
        ]);
       

        if ($response->successful()) {
            // Obtener el token de la respuesta
            $token = $response->json()['access_token'];

            // Guardar el token en caché por 60 minutos
            Cache::put('api_access_token', $token, now()->addMinutes(60));
            return $token;
        } else {
            // Manejo de error si no se obtiene el token
            abort(401, 'No se pudo autenticar');
        }
    }

    /**
     * Obtiene el token de acceso desde el caché o hace la autenticación si no está disponible.
     */
    public function getToken()
    {
        // Intenta obtener el token del caché
        $token = Cache::get('api_access_token');

        // Si no existe o ha expirado, obtén uno nuevo
        if (!$token) {
            // Si no hay token, vuelve a autenticar
            return $this->authenticate();
        }

        return $token;
    }

    /**
     * Consumir la API con el token de acceso y recibir datos como parámetro.
     */
    public function getDispoTraslados(Request $request)
    {
        // Obtén el token (lo recuperará del caché si está disponible)
        $token = $this->getToken();
     
        // Recuperar la URL base de la API desde el archivo .env
        $apiUrl = env('API_VM_URL') . '/getDispoTraslados';  // Ajusta el endpoint si es necesario

        // Obtener todos los datos del cuerpo de la solicitud sin validación
        $data = $request->all();  // El cuerpo del POST, tal como llega

        // Realiza la solicitud POST con el token y el cuerpo de la solicitud
        //print_r($data);
        
        $data['Tipo_servicio'] = 'T';

        
        
        $response = Http::withToken($token)
                        ->withHeaders([
                            'Content-Type' => 'application/json'
                        ])
                        ->post($apiUrl, $data);
        //print_r($response);

        return $response;
    }

    public function getDispoHoteles(Request $request)
    {
        // Obtén el token (lo recuperará del caché si está disponible)
        $token = $this->getToken();
     
        // Recuperar la URL base de la API desde el archivo .env
        $apiUrl = env('API_VM_URL') . '/getDispoHotels';  // Ajusta el endpoint si es necesario

        // Obtener todos los datos del cuerpo de la solicitud sin validación
        $data = $request->all();  // El cuerpo del POST, tal como llega

        // Realiza la solicitud POST con el token y el cuerpo de la solicitud
        //print_r($data);
        
        
        
        $response = Http::withToken($token)
                        ->withHeaders([
                            'Content-Type' => 'application/json'
                        ])
                        ->post($apiUrl, $data);
        //print_r($response);

        return $response;
    }
    public function getHotelinfo($id)
    {
        // Obtén el token (lo recuperará del caché si está disponible)
        $token = $this->getToken();
     
        // Recuperar la URL base de la API desde el archivo .env
        $apiUrl = env('API_VM_URL') . '/getHotelInfo/' . $id;  // Ajusta el endpoint si es necesario
 
        // Realizar la solicitud GET
        $response = Http::withToken($token)
        ->withHeaders([
            'Content-Type' => 'application/json'
        ])
        ->get($apiUrl);
            //print_r($response);

        return $response;
    }

    public function getDispoTours(Request $request)
    {
        // Obtén el token (lo recuperará del caché si está disponible)
        $token = $this->getToken();
     
        // Recuperar la URL base de la API desde el archivo .env
        $apiUrl = env('API_VM_URL') . '/getDispoTours';  // Ajusta el endpoint si es necesario
       
        // Obtener todos los datos del cuerpo de la solicitud sin validación
        $data = $request->all();  // El cuerpo del POST, tal como llega
        
        // Realiza la solicitud POST con el token y el cuerpo de la solicitud
        //print_r($data);
        
        $data['Tipo_servicio'] = 'TOU';

        
        
        $response = Http::withToken($token)
                        ->withHeaders([
                            'Content-Type' => 'application/json'
                        ])
                        ->post($apiUrl, $data);
        //print_r($response);

        return $response;
    }

    public function getTourInfo($id)
    {
        // Obtén el token (lo recuperará del caché si está disponible)
        $token = $this->getToken();
     
        // Recuperar la URL base de la API desde el archivo .env
        $apiUrl = env('API_VM_URL') . '/getTourInfo/' . $id;  // Ajusta el endpoint si es necesario
       
         // Realizar la solicitud GET
         $response = Http::withToken($token)
         ->withHeaders([
             'Content-Type' => 'application/json'
         ])
         ->get($apiUrl);
             //print_r($response);
 
         return $response;        
        
    }
    public function addCarrito(Request $request)
    {
        // Obtén el token (lo recuperará del caché si está disponible)
        $token = $this->getToken();
     
        // Recuperar la URL base de la API desde el archivo .env
        $apiUrl = env('API_VM_URL') . '/addCarrito';  // Ajusta el endpoint si es necesario
       
        // Obtener todos los datos del cuerpo de la solicitud sin validación
        $data = $request->all();  // El cuerpo del POST, tal como llega
        
        // Realiza la solicitud POST con el token y el cuerpo de la solicitud
        //print_r($data);
        //dd($data);
        
        
        $response = Http::withToken($token)
                        ->withHeaders([
                            'Content-Type' => 'application/json'
                        ])
                        ->post($apiUrl, $data);
        //print_r($response);
        // dd($response->status(), $response->body());

        return $response;        
    }

    public function showCarrito()
    {
        // Obtén el token (lo recuperará del caché si está disponible)
        $token = $this->getToken();
     
        // Recuperar la URL base de la API desde el archivo .env
        $apiUrl = env('API_VM_URL') . '/showCarrito';  // Ajusta el endpoint si es necesario
       
       
        // Realiza la solicitud GET con el token 
        
        
        $response = Http::withToken($token)
                        ->withHeaders([
                            'Content-Type' => 'application/json'
                        ])
                        ->get($apiUrl);
        //print_r($response);  

        return $response;        
    }

    public function borrarCarrito()
    {
        // Obtén el token (lo recuperará del caché si está disponible)
        $token = $this->getToken();
     
        // Recuperar la URL base de la API desde el archivo .env
        $apiUrl = env('API_VM_URL') . '/vaciarCarrito';  // Ajusta el endpoint si es necesario
       
       
        // Realiza la solicitud GET con el token 
        
        
        $response = Http::withToken($token)
                        ->withHeaders([
                            'Content-Type' => 'application/json'
                        ])
                        ->delete($apiUrl);
        //print_r($response);  
        //dd($response->status(), $response->body());

        return $response;        
    }

    public function confirmarReserva(Request $request)
    {
        // Obtén el token (lo recuperará del caché si está disponible)
        $token = $this->getToken();
     
        // Recuperar la URL base de la API desde el archivo .env
        $apiUrl = env('API_VM_URL') . '/confirmReserva';  // Ajusta el endpoint si es necesario
       
        // Obtener todos los datos del cuerpo de la solicitud sin validación
        $data = $request->all();  // El cuerpo del POST, tal como llega
        
        // Realiza la solicitud POST con el token y el cuerpo de la solicitud
        //print_r($data);
        
        $response = Http::withToken($token)
                        ->withHeaders([
                            'Content-Type' => 'application/json'
                        ])
                        ->post($apiUrl, $data);
        //print_r($response);

        return $response;
    }

    public function showReservaPorLoc($loc)
    {
        // Obtén el token (lo recuperará del caché si está disponible)
        $token = $this->getToken();
     
        // Recuperar la URL base de la API desde el archivo .env
        $apiUrl = env('API_VM_URL') . '/getReservaByLocalizador//?Localizador=' . $loc;  // Ajusta el endpoint si es necesario
       
       
        // Realiza la solicitud GET con el token 
        
        
        $response = Http::withToken($token)
                        ->withHeaders([
                            'Content-Type' => 'application/json'
                        ])
                        ->get($apiUrl);
        //print_r($response);  

        return $response;        
    }

   public function getVoucher($id)
    {
        // Obtén el token (lo recuperará del caché si está disponible)
        $token = $this->getToken();
     
        // Recuperar la URL base de la API desde el archivo .env
        $apiUrl = env('API_VM_URL') . '/voucher/?id=' . $id;  // Ajusta el endpoint si es necesario
       
       
        // Realiza la solicitud GET con el token 
        
        
        $response = Http::withToken($token)
                        ->withHeaders([
                            'Content-Type' => 'application/json'
                        ])
                        ->get($apiUrl);
        //print_r($response);  

        return $response;        
    }

}
