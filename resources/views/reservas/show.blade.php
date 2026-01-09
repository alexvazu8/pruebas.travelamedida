@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center text-primary mb-4">{{__("Detalle_reservas")}}</h1>

    {{-- Mostrar mensaje de éxito o error --}}
    @if(isset($mensaje))
        <div class="alert {{ $mensaje === 'Exito!!!' ? 'alert-success' : 'alert-danger' }} text-center">
            {{ $mensaje }}
        </div>
    @endif

    @php
    print_r($respuestas);
    @endphp

    {{-- Verificar si hay respuestas --}}
    @if(isset($respuestas) && count($respuestas) > 0)
        @foreach($respuestas as $reserva)
            <div class="card mb-4 shadow-sm border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title">{{__("Informacion_general_reservas")}}</h5>
                </div>
                <div class="card-body">
                    <p><strong>{{__("Localizador")}}:</strong> {{ $reserva['Localizador'] }}</p>
                    <p><strong>{{__("Importe_reserva")}}:</strong> ${{ number_format($reserva['Importe_Reserva'], 2) }}</p>
                    <p><strong>{{__("Nombre_titular_reserva")}}:</strong> {{ $reserva['Nombre_Cliente'] }} {{ $reserva['Apellido_Cliente'] }}</p>
                    <p><strong>{{__("Telefono_contacto")}}:</strong> {{ $reserva['Telefono_Cliente'] }}</p>
                    <p><strong>{{__("Email_contacto")}}:</strong> {{ $reserva['Email_contacto_reserva'] }}</p>
                    <p><strong>{{__("Comentarios")}}:</strong> {{ $reserva['Comentarios'] ?? 'N/A' }}</p>
                </div>
            </div>

            {{-- Sección de Detalle de Reservas --}}
            @if(!empty($reserva['detalle_reservas']))
                <div class="card mb-4 shadow-sm border-info">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title">{{__("Detalle_reservas")}}</h5>
                    </div>
                    <div class="card-body">
                        @foreach($reserva['detalle_reservas'] as $detalle)
                            <div class="mb-4 border-info">
                                <div class="service-details-border p-4 border border-primary rounded">
                                    {{-- Título "Detalles del Servicio" pegado al borde --}}
                                    @php
                                        $colorFondo = ($detalle['estado'] === 'Cancelado') ? 'rgb(255, 0, 4)' : '#007bff';
                                    @endphp

                                    <h5 class="text-center text-white font-weight-bold mb-0 p-3"
                                        style="background-color: {{ $colorFondo }}; border-radius: 8px 8px 0 0; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); margin-bottom: 0;">
                                        
                                        {{__("Detalle_servicio")}} <strong>ID:</strong> {{ $detalle['id'] }}

                                        @if($detalle['estado'] === 'Cancelado')
                                            <span class="text-white font-weight-bold mb-0 p-3">{{__("Cancelado")}}</span>
                                        @else
                                            <a href="{{ route('reservas.voucher', ['id' => $detalle['id']]) }}" class="text-white font-weight-bold mb-0 p-3">
                                                {{__("Ver_voucher")}}
                                            </a>
                                            <form action="{{ route('reservas.cancelar-servicio', ['id' => $detalle['id'], 'loc' => $reserva['Localizador']]) }}" 
                                                method="POST">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('¿ESTÁS SEGURO?\n\nEsta acción no se puede deshacer.')">
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                                                        <div class="text-start">
                                                            <i class="fas fa-times me-1"></i> Cancelar Servicio
                                                        </div>
                                                    </div>
                                                </button>
                                                
                                                {{-- Mostrar Penalidades --}}
                                                @if(isset($detalle['detalle_hotel']['politica']['penalidads']) && 
                                                    count($detalle['detalle_hotel']['politica']['penalidads']) > 0)
                                                    <div class="mt-3 p-3 border border-warning rounded bg-light">
                                                        <h6 class="text-warning">
                                                            <i class="fas fa-exclamation-circle me-2"></i>
                                                            {{__("Penalidades por cancelación")}}
                                                        </h6>
                                                        <div class="small">
                                                            @foreach($detalle['detalle_hotel']['politica']['penalidads'] as $penalidad)
                                                                <div class="text-warning">
                                                                    <strong>
                                                                        {{__("Cancelaciones_menos_de")}} {{ $penalidad['desde_noches_antes'] }} {{ __("Noches_anticipacion_penalidad_de") }} {{ $penalidad['porcentaje_penalidad_por_noche'] }}.
                                                                        {{ __("Cancelaciones_mas_de") }} {{ $penalidad['desde_noches_antes'] }} {{ __("Noches_sin_penalidad") }}
                                                                    </strong>

                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="mt-2 text-muted small">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            {{__("Las penalidades se aplican sobre el total de noches reservadas.")}}
                                                        </div>
                                                    </div>
                                                @endif
                                            </form>
                                        @endif
                                    </h5>


                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>ID:</strong> {{ $detalle['id'] }}</p>
                                            <p><strong>{{__("Tipo_servicio")}}:</strong> 
                                                @switch($detalle['Tipo_servicio'])
                                                    @case('T')
                                                        {{ __("Traslados") }}
                                                        @break
                                                    @case('H')
                                                        {{ __("Hoteles") }}
                                                        @break
                                                    @case('TOU')
                                                        {{ __("Tours") }}
                                                        @break
                                                    @default
                                                        Desconocido
                                                @endswitch
                                            </p>
                                            <p><strong>{{__("Email_encargado")}}:</strong> {{ $detalle['Email_encargado_reserva'] ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>{{__("Price_servicio")}}:</strong> ${{ number_format($detalle['Precio_Servicio'], 2) }}</p>
                                        </div>
                                    </div>
                                    {{-- Detalles del Hotel --}}
                                    @if(isset($detalle['detalle_hotel']))
                                    <div class="card mt-4 shadow-sm border-secondary">
                                        <div class="card-header bg-secondary text-white">
                                            <h6 class="card-title">{{__("Detalle_habitaciones")}}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <!-- Primera columna -->
                                                <div class="col-md-6">
                                                    <p><strong>{{__("Nombre_hotel")}}:</strong> {{ $detalle['detalle_hotel']['tipo_habitacion_hotel']['hotel']['Nombre_Hotel'] }}
                                                        @php
                                                            $estrellas = $detalle['detalle_hotel']['tipo_habitacion_hotel']['hotel']['estrellas']['estrellas'] ?? 0;
                                                            $tipo_categoria = $detalle['detalle_hotel']['tipo_habitacion_hotel']['hotel']['estrellas']['tipo_categoria'] ?? '';
                                                        @endphp

                                                        @if($tipo_categoria === 'E')
                                                            {{-- Mostrar estrellas --}}
                                                            @for ($i = 0; $i < $estrellas; $i++)
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="yellow" class="mr-1">
                                                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                                            </svg>    
                                                            @endfor
                                                        @elseif($tipo_categoria === 'L')
                                                            {{-- Mostrar llaves para apartamentos --}}
                                                            @for ($i = 0; $i < $estrellas; $i++)
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="blue" class="mr-1">
                                                                <path d="M6 2c-1.1 0-2 .9-2 2s.9 2 2 2h2v12h-2c-1.1 0-2 .9-2 2s.9 2 2 2h6c1.1 0 2-.9 2-2s-.9-2-2-2h-2V6h2c1.1 0 2-.9 2-2s-.9-2-2-2H6z"/>
                                                            </svg>
                                                            @endfor
                                                        @endif
                                                    </p>
                                                    <p><strong>{{__("Direccion")}}:</strong> {{ $detalle['detalle_hotel']['tipo_habitacion_hotel']['hotel']['Direccion_Hotel'] }}</p>
                                                    <p><strong>{{ __("Pais") }}:</strong> {{ $detalle['detalle_hotel']['tipo_habitacion_hotel']['hotel']['pais']['Nombre_Pais'] }}</p>
                                                    <p><strong>{{ __("Ciudad_hotel") }}:</strong> {{ $detalle['detalle_hotel']['tipo_habitacion_hotel']['hotel']['ciudad']['Nombre_Ciudad'] }}</p>
                                                    <p><strong>{{__("Zona")}}:</strong> {{ $detalle['detalle_hotel']['tipo_habitacion_hotel']['hotel']['zona']['Nombre_Zona'] }}</p>
                                                    <p><strong>{{__("Telefono")}}:</strong> {{ $detalle['detalle_hotel']['tipo_habitacion_hotel']['hotel']['Telefono_reservas_hotel'] }}</p>

                                                </div>
                                                <!-- Modal -->
                                                <div class="modal fade" id="mapaModal" tabindex="-1" aria-labelledby="mapaModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="mapaModalLabel">{{__("Location")}}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div id="mapa" style="width: 100%; height: 400px;"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Segunda columna -->
                                                <div class="col-md-6">
                                                    <p><strong>{{ __("Email_reservas") }}:</strong> {{ $detalle['detalle_hotel']['tipo_habitacion_hotel']['hotel']['email_reservas_hotel'] }}</p>
                                                    <p><strong>{{ __("Tipo_habitacion") }}</strong> {{ $detalle['detalle_hotel']['Nombre_Habitacion'] }}</p>
                                                    <p><strong>{{__("Regimen")}}:</strong> {{ $detalle['detalle_hotel']['Nombre_Regimen'] }}</p>
                                                    <p><strong>{{__("Precio_promedio_por_noche")}}:</strong> ${{ number_format($detalle['detalle_hotel']['Precio_promedio_por_noche'], 2) }}</p>
                                                    <p><strong>{{__("Noches")}}:</strong> {{ $detalle['detalle_hotel']['Cantidad_Noches'] }}</p>
                                                    <p><strong>{{__("Price_total")}}:</strong> ${{ number_format($detalle['detalle_hotel']['Precio_Total'], 2) }}</p>
                                                    <p><strong>{{__("Fecha_desde")}}:</strong> {{ date('d/m/Y', strtotime($detalle['detalle_hotel']['Fecha_In'])) }}</p>
                                                    <p><strong>{{__("Fecha_hasta")}}:</strong> {{ date('d/m/Y', strtotime($detalle['detalle_hotel']['Fecha_Out'])) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @endif

                                    {{-- Detalle Traslado --}}
                                    @if(!empty($detalle['detalle_traslado']))
                                        <div class="card mt-4 shadow-sm border-secondary">
                                            <div class="card-header bg-secondary text-white">
                                                <h6 class="card-title">{{__("Detalle_traslado")}}</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <p><strong>{{__("Cantidad_adultos")}}:</strong> {{ $detalle['detalle_traslado']['Cantidad_Adultos'] }}</p>
                                                        <p><strong>{{__("Cantidad_menores")}}:</strong> {{ $detalle['detalle_traslado']['Cantidad_Menores'] }}</p>
                                                        <p><strong>{{__("Fecha_servicio")}}:</strong> {{ $detalle['detalle_traslado']['fecha_servicio'] }}</p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <p><strong>{{__("Hora_servicio")}}:</strong> {{ $detalle['detalle_traslado']['hora_servicio'] }}</p>
                                                        <p><strong>{{__("Price_adulto")}}:</strong> ${{ number_format($detalle['detalle_traslado']['Precio_Adulto'], 2) }}</p>
                                                        <p><strong>{{__("Price_menor")}}:</strong> ${{ number_format($detalle['detalle_traslado']['Precio_Menor'], 2) }}</p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <p><strong>{{__("Price_total")}}:</strong> ${{ number_format($detalle['detalle_traslado']['Precio_Total'], 2) }}</p>
                                                        {{-- Nuevos campos agregados --}}
                                                        <p><strong>{{__("Marca_modelo")}}:</strong> {{ $detalle['detalle_traslado']['empresa_traslado_tipo_movilidade']['Marca_modelo'] ?? 'N/A' }}</p>
                                                        <p><strong>{{__("Maximo_maletas")}}:</strong> {{ $detalle['detalle_traslado']['empresa_traslado_tipo_movilidade']['Maletas_maximo'] ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    {{-- Detalle Tour --}}
                                    @if(!empty($detalle['detalle_tour']))
                                        <div class="card mt-4 shadow-sm border-secondary">
                                            <div class="card-header bg-secondary text-white">
                                                <h6 class="card-title">{{__("Detalle_tour")}}</h6>
                                            </div>
                                            <div class="card-body">
                                                @php
                                                    $tour = $detalle['detalle_tour']; 
                                                @endphp
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><strong>{{__("Tours")}}:</strong> {{ $tour['tour']['Nombre_tour'] ?? 'N/A' }}</p>
                                                        <p><strong>{{__("Horario_inicio")}}:</strong> {{ $tour['tour']['Horario_inicio'] ?? 'N/A' }}</p>
                                                        <p><strong>{{__("Horario_fin")}}:</strong> {{ $tour['tour']['Hora_fin'] ?? 'N/A' }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>{{__("Dias")}}:</strong> {{ $tour['tour']['cantidad_dias_tour'] ?? 'N/A' }}</p>
                                                        <p><strong>{{__("Noches")}}:</strong> {{ $tour['tour']['cantidad_noches_tour'] ?? 'N/A' }}</p>
                                                        <p><strong>{{__("Punto_encuentro")}}:</strong> {{ $tour['tour']['Punto_encuentro'] ?? 'N/A' }}</p>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><strong>{{__("Recojo_hotel")}}:</strong> {{ $tour['tour']['Recojo_hotel'] ? 'Sí' : 'No' }}</p>
                                                        <p><strong>{{__("Pais")}}:</strong> {{ $tour['tour']['pais']['Nombre_Pais'] ?? 'N/A' }}</p>
                                                        <p><strong>{{__("Accesible_discapacitados")}}:</strong> {{ $tour['tour']['Para_discapacitados'] ? 'Sí' : 'No' }}</p>
                                                        <p><strong>{{__("Entrega_agua")}}:</strong> {{ $tour['tour']['Entregan_agua'] ? 'Sí' : 'No' }}</p>
                                                        <div class="card-body">
                                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#detalleTourModal">
                                                                Ver Detalle del Tour
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><img width="100" src="{{ asset($tour['tour']['Foto_tours']) }}" alt="Foto del Tour" class="img-fluid rounded"></p>
                                                        <p><strong>{{__("Ciudad")}}:</strong> {{ $tour['tour']['ciudad']['Nombre_Ciudad'] ?? 'N/A' }}</p>
                                                        <p><strong>{{__("Zona")}}:</strong> {{ $tour['tour']['zona']['Nombre_Zona'] ?? 'N/A' }}</p>
                                                        <p><strong>{{__("Incluye_bano")}}:</strong> {{ $tour['tour']['Con_bano'] ? 'Sí' : 'No' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        {{-- Modal --}}
                                        <div class="modal fade" id="detalleTourModal" tabindex="-1" aria-labelledby="detalleTourModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="detalleTourModalLabel">{{__("Detalle_tour")}}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @php
                                                            $tour = $detalle['detalle_tour']; 
                                                        @endphp
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <p><strong>{{__("Tour")}}:</strong> {{ $tour['tour']['Nombre_tour'] ?? 'N/A' }}</p>
                                                                <p><strong>{{__("Horario_inicio")}}:</strong> {{ $tour['tour']['Horario_inicio'] ?? 'N/A' }}</p>
                                                                <p><strong>{{__("Horario_fin")}}:</strong> {{ $tour['tour']['Hora_fin'] ?? 'N/A' }}</p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p><strong>{{__("Dias")}}:</strong> {{ $tour['tour']['cantidad_dias_tour'] ?? 'N/A' }}</p>
                                                                <p><strong>{{__("Noches")}}:</strong> {{ $tour['tour']['cantidad_noches_tour'] ?? 'N/A' }}</p>
                                                                <p><strong>{{__("Punto_encuentro")}}:</strong> {{ $tour['tour']['Punto_encuentro'] ?? 'N/A' }}</p>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <p><strong>{{__("Recojo_hotel")}}:</strong> {{ $tour['tour']['Recojo_hotel'] ? 'Sí' : 'No' }}</p>
                                                                <p><strong>{{__("Pais")}}:</strong> {{ $tour['pais']['Nombre'] ?? 'N/A' }}</p>
                                                                <p><strong>{{__("Accesible_discapacitados")}}:</strong> {{ $tour['tour']['Para_discapacitados'] ? 'Sí' : 'No' }}</p>
                                                                <p><strong>{{__("Entrega_agua")}}:</strong> {{ $tour['tour']['Entregan_agua'] ? 'Sí' : 'No' }}</p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p><img width="100" src="{{ asset($tour['tour']['Foto_tours']) }}" alt="Foto del Tour" class="img-fluid rounded"></p>
                                                                <p><strong>{{__("Ciudad")}}:</strong> {{ $tour['ciudad']['Nombre'] ?? 'N/A' }}</p>
                                                                <p><strong>{{__("Zona")}}:</strong> {{ $tour['zona']['Nombre'] ?? 'N/A' }}</p>
                                                                <p><strong>{{__("Incluye_bano")}}:</strong> {{ $tour['tour']['Con_bano'] ? 'Sí' : 'No' }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                        <strong>{{__("Details")}}:</strong> @php echo $tour['tour']['Detalle_tour']; @endphp
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__("Close")}}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    No hay detalles de reserva disponibles.
                </div>
            @endif
        @endforeach
    @else
        <div class="alert alert-danger">
            No se encontraron datos de la reserva.
        </div>
    @endif
</div>

@endsection

@section('styles')
<!-- Agregar Leaflet.js -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

@endsection
@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        let modal = document.getElementById('mapaModal');
        let mapa = null; // Variable para almacenar el mapa

        modal.addEventListener('shown.bs.modal', function (event) {
            let button = event.relatedTarget;

            if (!button) return;

            let lat = button.getAttribute('data-lat') || 0;
            let lon = button.getAttribute('data-lon') || 0;
           
            console.log("Latitud:", lat, "Longitud:", lon);

            if (mapa) {
                // Si el mapa ya existe, simplemente movemos la vista
                mapa.setView([lat, lon], 15);
                
                // Actualizar marcador
                L.marker([lat, lon]).addTo(mapa)
                    .bindPopup("Ubicación del Hotel")
                    .openPopup();
            } else {
                // Crear el mapa solo si no existe
                mapa = L.map('mapa').setView([lat, lon], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(mapa);
                

                L.marker([lat, lon]).addTo(mapa)
                    .bindPopup("Ubicación del Hotel")
                    .openPopup();
            }

            // Redimensionar el mapa correctamente
            setTimeout(() => {
                mapa.invalidateSize();
            }, 300);
        });

        modal.addEventListener('hidden.bs.modal', function() {
            if (mapa) {
                mapa.remove(); // Eliminar el mapa para evitar duplicados
                mapa = null;
            }
        });
    });
</script>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
@endsection