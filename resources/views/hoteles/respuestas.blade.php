@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="text-center mb-4 text-primary">Hoteles Disponibles</h1>

    {{-- Verificar si hay errores --}}
    @if(isset($respuestas['error']))
        <div class="alert alert-danger">
            <strong>Error:</strong> {{ $respuestas['error'] }}
            <a href="{{ url('/hoteles') }}" class="btn btn-primary btn-lg mx-2">Hoteles</a>
        </div>
    @elseif(count($respuestas['data']) > 0)
        {{-- Lista de Hoteles --}}
        @foreach($respuestas['data'] as $hotel)
            <form method="POST" action="{{ route('hoteles.addCarrito') }}" class="mb-4 hotel-form" data-hotel-id="{{ $hotel['Id_Hotel'] }}">
                @csrf
                <input type="hidden" name="Tipo_servicio" value="H">
                <input type="hidden" name="Id_Hotel" value="{{ $hotel['Id_Hotel'] }}">
                <input type="hidden" name="Nombre_Hotel" value="{{ $hotel['Nombre_Hotel'] }}">
                <input type="hidden" name="Fecha_in" value="{{ $hotel['Fecha_desde'] }}">
                <input type="hidden" name="Fecha_out" value="{{ $hotel['Fecha_hasta'] }}">

                {{-- Tarjeta del Hotel --}}
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                {{ $hotel['Nombre_Hotel'] }}
                                <a href="#" class="text-white ms-2 hotel-info-link" 
                                   data-bs-toggle="modal" 
                                   data-bs-target="#hotelModal{{ $hotel['Id_Hotel'] }}"
                                   data-hotel-id="{{ $hotel['Id_Hotel'] }}">Info</a>
                            </h5>
                            <span class="text-warning">{{ str_repeat('⭐', $hotel['estrellas_id']) }}</span>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row align-items-start">
                            {{-- Foto del Hotel --}}
                            <div class="col-md-4 text-center">
                                <img src="{{ $hotel['Foto_Principal_Hotel'] }}" 
                                     alt="Foto de {{ $hotel['Nombre_Hotel'] }}" 
                                     class="img-fluid rounded" 
                                     style="max-height: 200px; object-fit: cover;">
                                <p class="mb-1"><strong>Dirección:</strong> {{ $hotel['Direccion_Hotel'] }}</p>
                            </div>

                            {{-- Información del Hotel --}}
                            <div class="col-md-8">
                                <p><strong>Fecha In:</strong> {{ $hotel['Fecha_desde'] }} <strong>Fecha Out:</strong> {{ $hotel['Fecha_hasta'] }}</p>

                                {{-- Lista de Habitaciones --}}
                                @if(isset($hotel['habitaciones']) && count($hotel['habitaciones']) > 0)
                                    <div class="mt-1">
                                        @foreach($hotel['habitaciones'] as $index => $grupoHabitaciones)
                                            <div class="me-1 mb-0" style="flex: 1 1 200px;">
                                                {{-- Resumen de Grupo de Habitaciones --}}
                                                <button type="button" 
                                                        class="btn btn-light w-100 text-start mb-2" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#collapseHabitacion{{ $hotel['Id_Hotel'] }}_{{ $index }}" 
                                                        aria-expanded="false" 
                                                        aria-controls="collapseHabitacion{{ $hotel['Id_Hotel'] }}_{{ $index }}">
                                                    <span class="fw-bold">Monto Total: $<span id="totalMonto{{ $hotel['Id_Hotel'] }}_{{ $index }}">{{ number_format($grupoHabitaciones[0]['Total'], 2) }}</span></span>
                                                    / Habitaciones: {{ $grupoHabitaciones[0]['Cantidad_habitaciones'] }} 
                                                    / Noches: {{ $grupoHabitaciones[0]['Cantidad_Noches'] }}
                                                    / {{ $grupoHabitaciones[0]['Cantidad_Adultos'] }} adultos, {{ $grupoHabitaciones[0]['Cantidad_Menores'] }} menores
                                                    / Detalle...
                                                </button>

                                                {{-- Detalle de Habitaciones --}}
                                                <div class="collapse" id="collapseHabitacion{{ $hotel['Id_Hotel'] }}_{{ $index }}">
                                                    <div class="d-flex flex-column">
                                                        @foreach($grupoHabitaciones as $habitacion)
                                                            <div class="form-check p-2 mb-1 border rounded bg-light">
                                                                <input 
                                                                    type="radio" 
                                                                    name="habitaciones[{{ $index }}]" 
                                                                    id="habitacion_{{ $hotel['Id_Hotel'] }}_{{ $habitacion['Id_tipo_habitacion_hotels'] }}" 
                                                                    class="form-check-input habitacion-radio" 
                                                                    value="{{ json_encode($habitacion) }}" 
                                                                    {{ $loop->first ? 'checked' : '' }}
                                                                    data-total="{{ $habitacion['Total'] }}" 
                                                                    data-hotel-id="{{ $hotel['Id_Hotel'] }}"
                                                                    data-index="{{ $index }}"
                                                                    required>
                                                                <label for="habitacion_{{ $hotel['Id_Hotel'] }}_{{ $habitacion['Id_tipo_habitacion_hotels'] }}" class="form-check-label">
                                                                    <strong>{{ $habitacion['Nombre_Habitacion'] }} {{ $habitacion['Nombre_Regimen'] }}</strong><br>
                                                                    {{ $habitacion['Cantidad_habitaciones'] }} habitaciones<br>
                                                                    {{ $habitacion['Cantidad_Adultos'] }} adultos, {{ $habitacion['Cantidad_Menores'] }} menores<br>
                                                                    {{ $habitacion['Cantidad_Noches'] }} noches<br>
                                                                    <span class="text-success">${{ number_format($habitacion['Total'], 2) }} total</span>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        {{-- Monto Total y Enviar --}}
                                        <div class="card-footer text-end bg-light d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0 me-3"><strong>Total:</strong> $<span class="total-acumulado" id="totalAcumulado-{{ $hotel['Id_Hotel'] }}">0.00</span></h5>
                                            <button type="submit" class="btn btn-primary px-4">Enviar al Carrito</button>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-info mt-3">
                                        No hay habitaciones disponibles en este hotel.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Modal Información del Hotel --}}
            <div class="modal fade" id="hotelModal{{ $hotel['Id_Hotel'] }}" tabindex="-1" aria-labelledby="hotelModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Información Completa - {{ $hotel['Nombre_Hotel'] }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="hotel-info-{{ $hotel['Id_Hotel'] }}">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
                        </div>
                        <div id="map-container-{{ $hotel['Id_Hotel'] }}" style="height: 300px; width: 100%;" class="mt-3 mb-3"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info">
            No se encontraron hoteles disponibles.
        </div>
    @endif
</div>

<!-- Incluir CSS de Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>

<!-- Incluir JavaScript de Leaflet -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== FUNCIONALIDAD PARA LOS TOTALES ==========
    function actualizarTotalPorHotel(hotelId) {
        let totalAcumulado = 0;
        
        // Seleccionar solo los radios del hotel específico
        document.querySelectorAll(`.habitacion-radio[data-hotel-id="${hotelId}"]:checked`).forEach(radio => {
            totalAcumulado += parseFloat(radio.getAttribute('data-total'));
            
            // Actualizar el monto del grupo específico
            const index = radio.getAttribute('data-index');
            document.getElementById(`totalMonto${hotelId}_${index}`).textContent = 
                parseFloat(radio.getAttribute('data-total')).toFixed(2);
        });
        
        // Actualizar el total acumulado del hotel
        document.getElementById(`totalAcumulado-${hotelId}`).textContent = totalAcumulado.toFixed(2);
    }

    // Inicializar todos los formularios
    document.querySelectorAll('.hotel-form').forEach(form => {
        const hotelId = form.getAttribute('data-hotel-id');
        actualizarTotalPorHotel(hotelId);
    });

    // Evento para cambios en los radio buttons
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('habitacion-radio')) {
            const hotelId = e.target.getAttribute('data-hotel-id');
            actualizarTotalPorHotel(hotelId);
        }
    });

    // ========== FUNCIONALIDAD PARA LOS MODALES ==========
    document.querySelectorAll('.hotel-info-link').forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            const hotelId = this.getAttribute('data-hotel-id');
            const modalBody = document.getElementById(`hotel-info-${hotelId}`);
            
            // Mostrar spinner de carga
            modalBody.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando información del hotel...</p>
                </div>
            `;

            // Obtener datos del hotel
            fetch(`/hoteles/info/${hotelId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Error en la respuesta');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const hotel = data.hotel;
                        
                        // Construir el contenido del modal
                        let modalContent = `
                            <h5>${hotel.Nombre_Hotel} ${'⭐'.repeat(hotel.estrellas.estrellas)}</h5>
                            <p>${hotel.ciudad.Nombre_Ciudad}, ${hotel.pais.Nombre_Pais}</p>
                            <p><strong>Dirección:</strong> ${hotel.Direccion_Hotel}</p>
                            <p><strong>Descripción:</strong> ${hotel.Descripcion_Hotel}</p>

                            <h5 class="mt-4">Fotos del Hotel</h5>
                            <div class="gallery">
                                <img src="${hotel.Foto_Principal_Hotel}" 
                                     alt="Foto principal de ${hotel.Nombre_Hotel}" 
                                     class="img-fluid mb-3 rounded" 
                                     style="max-height: 300px; object-fit: cover;">
                        `;

                        // Agregar fotos adicionales si existen
                        if (hotel.fotos_hotels && hotel.fotos_hotels.length > 0) {
                            modalContent += hotel.fotos_hotels.map(foto => `
                                <img src="${foto.Foto_Hotel}" 
                                     alt="Foto adicional de ${hotel.Nombre_Hotel}" 
                                     class="img-fluid mb-3 rounded" 
                                     style="max-height: 200px; object-fit: cover;">
                            `).join('');
                        }

                        modalContent += `
                            </div>

                            <h5 class="mt-4">Facilidades y Servicios</h5>
                            <ul class="list-group mb-3">
                        `;

                        // Agregar servicios si existen
                        if (hotel.hotel_facilidades_y_servicios && hotel.hotel_facilidades_y_servicios.length > 0) {
                            modalContent += hotel.hotel_facilidades_y_servicios.map(servicio => `
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    ${servicio.texto_facilidad}
                                    <span class="badge bg-primary rounded-pill">${servicio.costo} ${servicio.moneda}</span>
                                </li>
                            `).join('');
                        } else {
                            modalContent += `<li class="list-group-item">No hay servicios registrados</li>`;
                        }

                        modalContent += `
                            </ul>
                        `;

                        modalBody.innerHTML = modalContent;

                        // Inicializar el mapa
                        const mapContainer = document.getElementById(`map-container-${hotelId}`);
                        mapContainer.innerHTML = ''; // Limpiar contenedor

                        const map = L.map(`map-container-${hotelId}`).setView(
                            [hotel.Latitud, hotel.Longitud], 
                            15
                        );

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                        }).addTo(map);

                        L.marker([hotel.Latitud, hotel.Longitud])
                            .addTo(map)
                            .bindPopup(`<b>${hotel.Nombre_Hotel}</b><br>${hotel.Direccion_Hotel}`)
                            .openPopup();

                        // Ajustar el mapa cuando el modal se muestre completamente
                        const modal = document.getElementById(`hotelModal${hotelId}`);
                        modal.addEventListener('shown.bs.modal', function() {
                            setTimeout(() => {
                                map.invalidateSize();
                            }, 300);
                        });

                    } else {
                        modalBody.innerHTML = `
                            <div class="alert alert-danger">
                                ${data.message || 'Error al cargar la información del hotel'}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = `
                        <div class="alert alert-danger">
                            Error al cargar la información del hotel. Por favor intente nuevamente.
                        </div>
                    `;
                });
        });
    });
});
</script>
@endsection