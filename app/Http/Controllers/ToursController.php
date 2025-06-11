<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class ToursController extends Controller
{
      /**
     * Mostrar el formulario para obtener la disponibilidad de tours.
     */
    public function index()
    {
        // Aquí puedes obtener la lista de ciudades y zonas si es necesario.
        //como obtener ciudades con el modelo Ciudad
        $ciudades = Ciudad::all();
        //enviar $ciudades compact


        // Si no necesitas usar la base de datos para ciudades y zonas, puedes obtenerlas directamente desde la API.
        return view('tours.index', compact('ciudades'));
    }
    /**
     * Obtener la disponibilidad de tour.
     */
    public function obtenerDisponibilidad(Request $request)
    {  
        // Aquí puedes obtener la disponibilidad de tour según la ciudad y zona seleccionadas.
        //como obtener disponibilidad de tour con el modelo Tour
        // Llamamos al método getDispoTours del ApiController
        $apiController = new ApiController();
        
        $response = $apiController->getDispoTours($request);
        //dd($response);
        if (!$response->successful()) {
            // Procesar los datos obtenidos de la API
            //aqui no fue satisfactorio, hay errores:
            $jsonResponse= response()->json($response->json());
            $data = json_decode($jsonResponse->getContent(), true);

             //dd($data);
            return view('tours.respuestas', ['respuestas' => $data]);
        } else {
            $jsonResponse= response()->json($response->json());
            //print_r(response()->json($jsonResponse));
            $data = json_decode($jsonResponse->getContent(), true);
             //dd($data);
           
            return view('tours.respuestas', ['respuestas' => $data]);
        
        } 

    
    }   
    public function tourInfo($id)
    {
        // Aquí puedes obtener la información de un tour según su ID.
         // Llamamos al método getTourInfo del ApiController
         $apiController = new ApiController();
        
         $response = $apiController->getTourInfo($id);
         //dd($response);
         if (!$response->successful()) {
            $jsonResponse = response()->json($response->json());
            $data = json_decode($jsonResponse->getContent(), true);
            return response()->json(['success' => false, 'message' => 'No se pudo obtener la información del hotel.']);
        } else {
            $jsonResponse = response()->json($response->json());
            $data = json_decode($jsonResponse->getContent(), true);
            return response()->json(['success' => true, 'tour' => $data]);
        }
    }

    public function addCarritoTours(Request $request)
    {
        // Verificar si 'Edad_menores' existe en el request
        if ($request->has('Edad_menores')) {
            // Decodificar 'Edad_menores' si es una cadena JSON
            $edad_menores = json_decode($request->Edad_menores, true); // true para que sea un array

            // Verificar si la decodificación fue exitosa y es un array
            if (is_array($edad_menores)) {
                // Si es un array, agregar al request
                $request->merge(['Edad_menores' => $edad_menores]);
            } else {
                // Si no se pudo decodificar correctamente, asignar un valor vacío o manejar el error
                $request->merge(['Edad_menores' => []]); // Puedes manejar el error de otra manera
            }
        }

        // Verificación del request con los datos decodificados
       // dd($request->all());  // Aquí deberías ver que 'Edad_menores' ahora es un array

        // Llamar al método addCarrito del ApiController
        $apiController = new ApiController();
        $response = $apiController->addCarrito($request);

        // Verificar si la respuesta es exitosa
        if (!$response->successful()) {
            $jsonResponse = response()->json($response->json());
            $data = json_decode($jsonResponse->getContent(), true);

            return view('tours.errorCarrito', ['respuestas' => $data]);
        } else {
            $jsonResponse = response()->json($response->json());
            $data = json_decode($jsonResponse->getContent(), true);

            // Redirigir a la ruta 'carritos.show'
            return redirect()->route('carritos.show');
        }
    }

}
