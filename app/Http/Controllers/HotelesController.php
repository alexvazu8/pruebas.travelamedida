<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use Illuminate\Http\Request;

class HotelesController extends Controller
{
       /**
     * Mostrar el formulario para obtener la disponibilidad de hoteles.
     */
    public function index()
    {
        // Aquí puedes obtener la lista de ciudades y zonas si es necesario.
        // Si no necesitas usar la base de datos para ciudades y zonas, puedes obtenerlas directamente desde la API.
        $ciudades = Ciudad::all();
        //enviar $ciudades compact
        return view('hoteles.index', compact('ciudades'));
    }
    
    /**
     * Obtener la disponibilidad de hoteles.
     */
    public function obtenerDisponibilidad(Request $request)
    {
        // Llamamos al método getDispoTraslados del ApiController
        $apiController = new ApiController();
        $response = $apiController->getDispoHoteles($request);
        
        if (!$response->successful()) {
            // Procesar los datos obtenidos de la API
            //aqui no fue satisfactorio, hay errores:
            $jsonResponse= response()->json($response->json());
            $data = json_decode($jsonResponse->getContent(), true);

             //dd($data['error']);
            return view('hoteles.respuestas', ['respuestas' => $data]);
        } else {
            $jsonResponse= response()->json($response->json());
            //print_r(response()->json($jsonResponse));
            $data = json_decode($jsonResponse->getContent(), true);
            // dd($data);
           
            return view('hoteles.respuestas', ['respuestas' => $data]);
        
        }
        
    }
    public function hotelInfo($id)
    {
        // Llamamos al método getHotelinfo del ApiController
        $apiController = new ApiController();
        $response = $apiController->getHotelinfo($id);

        if (!$response->successful()) {
            $jsonResponse = response()->json($response->json());
            $data = json_decode($jsonResponse->getContent(), true);
            return response()->json(['success' => false, 'message' => 'No se pudo obtener la información del hotel.']);
        } else {
            $jsonResponse = response()->json($response->json());
            $data = json_decode($jsonResponse->getContent(), true);
            return response()->json(['success' => true, 'hotel' => $data]);
        }
    }

    public function addCarritoHotel(Request $request)
    {     // dd($request->all());
         // Llamamos al método addCarrito del ApiController

        // Decodificar todas las habitaciones
        $habitaciones = array_map(function ($habitacion) {
            return json_decode($habitacion, true);
        }, $request->habitaciones);
        

        // Reemplazar el valor de habitaciones en el request con el arreglo de habitaciones decodificadas
        $request->merge(['habitaciones' => $habitaciones]);

        //dd($request->all());

         $apiController = new ApiController();
         $response = $apiController->addCarrito($request);
         
         if (!$response->successful()) {
            //dd($request);
            // Procesar los datos obtenidos de la API
            //aqui no fue satisfactorio, hay errores:
            $jsonResponse= response()->json($response->json());
            $data = json_decode($jsonResponse->getContent(), true);

             //dd($data['error']);
            return view('hoteles.errorCarrito', ['respuestas' => $data]);
        } else {
            $jsonResponse= response()->json($response->json());
            //print_r(response()->json($jsonResponse));
            $data = json_decode($jsonResponse->getContent(), true);
             //dd($data);
           // Redirigir a la ruta 'carritos.show' pasando algún parámetro si es necesario
            return redirect()->route('carritos.show');
            
        
        } 
         

    }
}
