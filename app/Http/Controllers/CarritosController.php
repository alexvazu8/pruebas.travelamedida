<?php

namespace App\Http\Controllers;

use App\Models\TiposCambio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CarritosController extends Controller
{
    protected function tc_a_guaranies()
    {
        $ultimoTipoCambio = TiposCambio::where('moneda_origen', 'USD')
                    ->where('moneda_destino', 'PYG')
                    ->latest('fecha_validez') // Ordena por fecha más reciente
                    ->first();
                    //print_r($ultimoTipoCambio['tasa_cambio']);
                    return number_format($ultimoTipoCambio['tasa_cambio'], 2, '.', '');
    }
    //hacer la funcion show
    public function show()
    {  $error = session('error');
        $tc_grs=$this->tc_a_guaranies();
        //usar ApiController
        $apiController = new ApiController();
        $response = $apiController->showCarrito();
        if (!$response->successful()) {
            //dd($request);
            // Procesar los datos obtenidos de la API
            //aqui no fue satisfactorio, hay errores:
            $jsonResponse= response()->json($response->json());
            $data = json_decode($jsonResponse->getContent(), true);


            return view('carritos.show', ['respuestas' => $data,'mensaje'=>'Error o Carrito vacio.!!!','tipoCambioGuaranies'=>$tc_grs]);
        } else {
            $jsonResponse= response()->json($response->json());
            //print_r(response()->json($jsonResponse));
            $data = json_decode($jsonResponse->getContent(), true);
             //dd($data);
             $mensaje='Exito!!!';
             
             if(isset($error)){$mensaje=$error;} 
            return view('carritos.show', ['respuestas' => $data,'mensaje'=>$mensaje,'tipoCambioGuaranies'=>$tc_grs]);
        
        } 
    }

    //Funcion Delete

    public function borrar()
    {
        //usar ApiController
        $apiController = new ApiController();
        $response = $apiController->borrarCarrito();
        if (!$response->successful()) {
            //dd($request);
            // Procesar los datos obtenidos de la API
            //aqui no fue satisfactorio, hay errores:
            $jsonResponse= response()->json($response->json());
            $data = json_decode($jsonResponse->getContent(), true);


            return view('carritos.show', ['respuestas' => $data,'mensaje'=>'Error o Carrito vacio.!!!']);
        } else {
            $jsonResponse= response()->json($response->json());
            $userId = Auth::id();
            // Limpiar caché relacionada con el token anterior
            Cache::forget('api_access_token_'.$userId);
            Cache::forget('api_access_token_expire_at_'.$userId);
            session()->regenerate();
            //print_r(response()->json($jsonResponse));
            $data = json_decode($jsonResponse->getContent(), true);
             //dd($data);
             // Acceder a los valores específicos
            $token = $data['new_token'];
            $expireInMinutes = $data['expires_in']/60;
            Cache::put('api_access_token_'.$userId, $token, now()->addMinutes($expireInMinutes));
            Cache::put('api_access_token_expire_at_'.$userId, now()->addMinutes($expireInMinutes));

           
            return view('carritos.show', ['respuestas' => $data,'mensaje'=>'Carrito Vacio!!!']);
        
        } 
    }
    

}
