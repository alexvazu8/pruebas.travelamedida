<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Models\Ciudad;
use App\Models\Pais;
use App\Models\ServicioTraslado;
use App\Models\Zona;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrasladosController extends Controller
{
    /**
     * Zonas Origen por ciudad
     */
    public function getZonasOrigen($idciudad,$tipo_servicio_transfer)
    { 
        $zonas_origen = ServicioTraslado::where('tipo_servicio_transfer', $tipo_servicio_transfer)
        ->whereHas('zonaOrigen', function($query) use ($idciudad) {
            $query->where('ciudad_id_ciudad', $idciudad);
        })
        ->with('zonaOrigen')
        ->get()
        ->pluck('zonaOrigen')
        ->unique('id_zona');

        
        // Retornar las zonas en formato JSON
        return response()->json(['zonas' => $zonas_origen]);
    }

    /**
     * Zonas destino por zona origen
     */
    public function getZonasDestinoPorOrigen($idzona)
    {
        // Obtener las zonas de destino con sus detalles, no solo los ID
        $zonas_destino = ServicioTraslado::where('zona_origen_id', $idzona)
                                         ->with('zonaDestino')  // Usar la relación definida en el modelo
                                         ->get()
                                         ->pluck('zonaDestino')
                                         ->unique('id_zona');  // Obtener las zonas destino completas
    
         // Retornar las zonas de destino en formato JSON
        return response()->json(['zonas' => $zonas_destino]);
    }

    /**
     * Mostrar el formulario para obtener la disponibilidad de traslados.
     */
    public function index()
    {
        // Aquí puedes obtener la lista de ciudades y zonas si es necesario.
        // Si no necesitas usar la base de datos para ciudades y zonas, puedes obtenerlas directamente desde la API.
        $ciudades = Ciudad::where('pais_id_pais','1')->get();
        //enviar $ciudades compact
        $paises= Pais::all();
        

        return view('traslados.index', compact('ciudades', 'paises'));

    }

    /**
     * Obtener la disponibilidad de traslados.
     */
    public function obtenerDisponibilidad(Request $request)
    {   // print_r($request->all());
        $request['Fecha_disponible'] = Carbon::createFromFormat('Y-m-d', $request->Fecha_disponible)->format('d/m/Y');
            // Reglas base
        $rules = [
            'Tipo_servicio_transfer' => 'required|string|in:IN,OUT,HTH',
            'Fecha_disponible' => 'required|date_format:d/m/Y',
            'Ciudad_Id_Ciudad' => 'required|integer|exists:ciudades,id_ciudad',
            'Zona_Origen_id' => 'required|integer|exists:zonas,id_zona',
            'Zona_Destino_id' => 'required|integer|exists:zonas,id_zona|different:Zona_Origen_id',
            'hora_servicio' => 'required|date_format:H:i',
            'Cantidad_menores' => 'required|integer|min:0|max:5',
            'Edad_menores' => 'nullable|array',
        ];

        // Generar reglas dinámicas
        for ($i = 1; $i <= ($request->Cantidad_menores ?? 0); $i++) {
            $rules["Edad_menores.$i"] = 'required|numeric|min:0|max:12';
        }

        // Mensajes personalizados
        $messages = [
            'Cantidad_menores.required' => 'Debe indicar la cantidad de menores.',
            'Cantidad_menores.integer' => 'La cantidad de menores debe ser un número entero.',
            'Cantidad_menores.min' => 'La cantidad de menores no puede ser menor que 0.',
            'Cantidad_menores.max' => 'La cantidad de menores no puede ser mayor que 5.',
            'Edad_menores.*.required' => 'Debe proporcionar la edad para cada menor.',
            'Edad_menores.*.numeric' => 'La edad debe ser un número válido.',
            'Edad_menores.*.min' => 'La edad mínima para un menor es 0.',
            'Edad_menores.*.max' => 'La edad máxima para un menor es 12.',
        ];

        // Validar los datos
        $validated = $request->validate($rules, $messages);
        $fechaDisponibilidad = Carbon::createFromFormat('d/m/Y', $request->Fecha_disponible)->format('Y-m-d');
       
         $request['Fecha_disponible']=$fechaDisponibilidad;
        // Llamamos al método getDispoTraslados del ApiController
        $apiController = new ApiController();
        $response = $apiController->getDispoTraslados($request);
        
        if (!$response->successful()) {
            // Procesar los datos obtenidos de la API
            //aqui no fue satisfactorio, hay errores:
            $jsonResponse= response()->json($response->json());
            $data = json_decode($jsonResponse->getContent(), true);

             //dd($data['error']);
            return view('traslados.errorRespuestas', ['respuestas' => $data]);
        } else {
            $jsonResponse= response()->json($response->json());
            //print_r(response()->json($jsonResponse));
            $data = json_decode($jsonResponse->getContent(), true);
            // dd($data);
           
            return view('traslados.respuestas', ['respuestas' => $data]);
        
        } 
    }
    public function addCarritoTraslados(Request $request)
    {      

         // Llamamos al método addCarrito del ApiController
         $apiController = new ApiController();
         $response = $apiController->addCarrito($request);
         if (!$response->successful()) {
            //dd($request);
            // Procesar los datos obtenidos de la API
            //aqui no fue satisfactorio, hay errores:
            $jsonResponse= response()->json($response->json());
            $data = json_decode($jsonResponse->getContent(), true);

             //dd($data['error']);
            return view('traslados.respuestaCarrito', ['respuestas' => $data]);
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
