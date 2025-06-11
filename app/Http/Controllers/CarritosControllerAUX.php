<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CarritosController extends Controller
{
    //hacer la funcion show
    public function show()
    {
        //usar ApiController
        $apiController = new ApiController();
        $response = $apiController->showCarrito();
        if (!$response->successful()) {
            //dd($request);
            // Procesar los datos obtenidos de la API
            //aqui no fue satisfactorio, hay errores:
            $jsonResponse= response()->json($response->json());
            $data = json_decode($jsonResponse->getContent(), true);


            return view('carritos.show', ['respuestas' => $data,'mensaje'=>'Error o Carrito vacio.!!!']);
        } else {
            $jsonResponse= response()->json($response->json());
            //print_r(response()->json($jsonResponse));
            $data = json_decode($jsonResponse->getContent(), true);
             //dd($data);
           
            return view('carritos.show', ['respuestas' => $data,'mensaje'=>'Exito!!!']);
        
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
            //print_r(response()->json($jsonResponse));
             //dd($data);
             // Limpiar caché relacionada con el token anterior
            Cache::forget('api_access_token');
            Cache::forget('api_access_token_expire_at');
            session()->regenerate();
           
            //print_r(response()->json($jsonResponse));
            $data = json_decode($jsonResponse->getContent(), true);
             //dd($data);
             // Acceder a los valores específicos
            $token = $data['new_token'];
            $expireInMinutes = $data['expires_in']/60;
            Cache::put('api_access_token', $token, now()->addMinutes($expireInMinutes));
            Cache::put('api_access_token_expire_at', now()->addMinutes($expireInMinutes));

           
            return view('carritos.show', ['respuestas' => $data,'mensaje'=>'Carrito Vacio!!!']);
        
        } 
    }
    

}
