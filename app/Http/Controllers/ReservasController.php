<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;

class ReservasController extends Controller
{

          /**
     * Genera un UUID v4 y se asegura de que no exista en la tabla reservas.
     *
     * @return string
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

        return $guid;
    }

   //Funcion confirmar

   public function confirmar(Request $request)
   {   //Solo debe ingresar si esta hecho el pago.
       
       //usar ApiController
       $apiController = new ApiController();
       $response = $apiController->confirmarReserva($request);
       if (!$response->successful()) {
           //dd($request);
           // Procesar los datos obtenidos de la API
           //aqui no fue satisfactorio, hay errores:
           $jsonResponse= response()->json($response->json());
           $data = json_decode($jsonResponse->getContent(), true);

           //dd($data);
            return view('reservas.error', ['respuestas' => $data,'mensaje'=>'Error o Carrito vacio.!!!']);
       } else {
                    
           $jsonResponse= response()->json($response->json());
           //print_r(response()->json($jsonResponse));
           // Limpiar caché relacionada con el token anterior
            Cache::forget('api_access_token');
            Cache::forget('api_access_token_expire_at');
           $data = json_decode($jsonResponse->getContent(), true);
           //regenerar session de la pagina para el usuario que esta con login
           $request->session()->regenerate();
           //dd($data);
            // Acceder a los valores específicos
            $token = $data['new_token'];
            $expireInMinutes = $data['expires_in']/60;
            Cache::put('api_access_token', $token, now()->addMinutes($expireInMinutes));
            Cache::put('api_access_token_expire_at', now()->addMinutes($expireInMinutes));
            
            // ok aqui puedo guardar en mi propia BD con el id de usuario que compro, de esta forma
            //puede ver su reserva.
            //reservar con Reserva
            $datosLimpios = collect($data[0])->only([
                'Localizador', 'Importe_Reserva', 'Nombre_Cliente','Apellido_Cliente', 'Email_contacto_reserva', 'Comentarios', 'Usuario_id'
            ])->toArray();
            // Asigna el usuario autenticado
            $datosLimpios['Usuario_id'] = Auth::id(); 
            //  Genera un GUID único de 36 chars
            $datosLimpios['guid'] = $this->generateUniqueGuid();
            $reserva=Reserva::create($datosLimpios);
            //dd($reserva);
            //debe ir a una ruta para ejecutar, luego una vista
            $loc=$reserva['Localizador'];
            //dd($loc);
            return redirect()->route('reservas.showReserva', ['loc' => $loc]);
           //return view('reservas.show', ['respuestas' => $data,'local_respuestas'=> $reserva,'mensaje'=>'Exito!!!']);
       
       } 
   }

   public function showReserva($loc)
   {   //colocar el modelo Reserva
       
       //usar ApiController
       $apiController = new ApiController();
       
       $response = $apiController->showReservaPorLoc($loc);
       
       if (!$response->successful()) {
           //dd($request);
           // Procesar los datos obtenidos de la API
           //aqui no fue satisfactorio, hay errores:
           $jsonResponse= response()->json($response->json());
           $data = json_decode($jsonResponse->getContent(), true);

           //dd($data);
            return view('reservas.error', ['respuestas' => $data,'mensaje'=>'Error o Reserva no existe o Usuario no tiene permiso.!!!']);
       } else {
           $jsonResponse= response()->json($response->json());
           //print_r(response()->json($jsonResponse));
           $data = json_decode($jsonResponse->getContent(), true);
           // print_r($data);
            // ok aqui puedo guardar en mi propia BD con el id de usuario que compro, de esta forma
            //puede ver su reserva.
            //reservar con Reserva
            $datosLimpios = collect($data[0])->only([
                'Localizador', 'Importe_Reserva', 'Nombre_Cliente', 'Email_contacto_reserva', 'Comentarios', 'Usuario_id'
            ])->toArray();

            // Procesar la relación detalle_reservas
            $datosLimpios['detalleReservas'] = collect($data[0]['detalle_reservas'] ?? [])->map(function ($detalle) {
                return collect($detalle)->only([
                    'id', 'Precio_Servicio', 'Reserva_Id_reserva', 'Usuario_id', 'Tipo_servicio', 'Costo_servicio', 'Email_encargado_reserva'
                ])->merge([
                    'detalleHotel' => !empty($detalle['detalle_hotel']) ? collect($detalle['detalle_hotel'])->except(['created_at', 'updated_at']) : null,
                    'detalleTour' => !empty($detalle['detalle_tour']) ? collect($detalle['detalle_tour'])->except(['created_at', 'updated_at']) : null,
                    'detalleTraslado' => !empty($detalle['detalle_traslado']) ? collect($detalle['detalle_traslado'])->only([
                        'id', 'Cantidad_Adultos', 'Cantidad_Menores', 'detalle_reserva_id', 'Empresa_traslados_tipo_movilidades_id', 
                        'fecha_servicio', 'hora_servicio', 'Precio_Adulto', 'Precio_Menor', 'Precio_Total'
                    ]) : null,
                ])->toArray();
            })->toArray();

            // Respuesta limpia
            //print_r($datosLimpios);
            // Asigna el usuario autenticado
            $datosLimpios['Usuario_id'] = Auth::id(); 
            //dd($datosLimpios);
            $reserva=Reserva::where('Usuario_id',$datosLimpios['Usuario_id'])
                     ->where('Localizador',$datosLimpios['Localizador'])
                     ->first();
            
            if(isset($reserva['Localizador']))
            {
                 //debe ir a una ruta para ejecutar, luego una vista
               // dd($datosLimpios);
              return view('reservas.show', ['respuestas' => $data,'local_respuestas'=> $reserva,'mensaje'=>'Exito!!!']);
            } 
            else
            { //error no existe ese localizador.
                return view('reservas.error', ['respuestas' => $data,'mensaje'=>'Localizador no tiene permiso.!!!']);
               
            }    
           
       
       } 
   }

   public function voucher($id)
   {
     //usar ApiController
     $apiController = new ApiController();
       
     $response = $apiController->getVoucher($id);

     if(!$response->successful()) {
        //dd($request);
        // Procesar los datos obtenidos de la API
        //aqui no fue satisfactorio, hay errores:
        $jsonResponse= response()->json($response->json());
        $data = json_decode($jsonResponse->getContent(), true);

        //dd($data);
         return view('reservas.error', ['respuestas' => $data,'mensaje'=>'Error o Voucher no existe o Usuario no tiene permiso.!!!']);
    } 
    else {
        $jsonResponse= response()->json($response->json());
        //print_r(response()->json($jsonResponse));
        $data = json_decode($jsonResponse->getContent(), true);
       // Enviar los datos a la vista
        return view('reservas.showVoucher')->with('data', $data);
    }

   }

   
}
