@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="text-center mb-4 text-primary">Hoteles Disponibles</h1>

    {{-- Verificar si hay errores --}}
    @if(isset($respuestas['error']))
        <div class="alert alert-danger">
            <strong>Error:</strong> {{ $respuestas['error'] }}
            <a href="{{ url('/hoteles') }}" class="btn btn-primary btn-lg mx-2">Hoteles</a>
            {{ print_r(old(), true) }}
        </div>
    @elseif(count($respuestas['data']) > 0)
        {{-- Lista de Hoteles --}}
        @foreach($respuestas['data'] as $hotel)
            <form method="POST" action="{{ route('hoteles.addCarrito') }}" class="mb-4">
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
                                <a href="#" data-bs-toggle="modal" data-bs-target="#hotelModal{{ $hotel['Id_Hotel'] }}" class="text-white ms-2">Info</a>
                            </h5>
                            <span class="text-warning">{{ str_repeat('⭐', $hotel['estrellas_id']) }}</span>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row align-items-start">
                            {{-- Foto del Hotel --}}
                            <div class="col-md-4 text-center">
                                <img src="{{ $hotel['Foto_Principal_Hotel'] }}" alt="Foto de {{ $hotel['Nombre_Hotel'] }}" class="img-fluid rounded" style="max-height: 200px; object-fit: cover;">
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
                                                <button type="button" class="btn btn-light w-100 text-start mb-2" data-bs-toggle="collapse" data-bs-target="#collapseHabitacion{{ $index }}" aria-expanded="false" aria-controls="collapseHabitacion{{ $index }}">
                                                    <span class="fw-bold">Monto Total: $<span id="totalMonto{{ $index }}">{{ number_format($grupoHabitaciones[0]['Total'], 2) }}</span></span>
                                                    / Habitaciones: {{ $grupoHabitaciones[0]['Cantidad_habitaciones'] }} 
                                                    / Noches: {{ $grupoHabitaciones[0]['Cantidad_Noches'] }}
                                                    / {{ $grupoHabitaciones[0]['Cantidad_Adultos'] }} adultos, {{ $grupoHabitaciones[0]['Cantidad_Menores'] }} menores
                                                    / Detalle...
                                                </button>

                                                {{-- Detalle de Habitaciones --}}
                                                <div class="collapse" id="collapseHabitacion{{ $index }}">
                                                    <div class="d-flex flex-column">
                                                        @foreach($grupoHabitaciones as $habitacion)
                                                            <div class="form-check p-2 mb-1 border rounded bg-light">
                                                                <input 
                                                                    type="radio" 
                                                                    name="habitaciones[{{ $index }}]" 
                                                                    id="habitacion_{{ $habitacion['Id_tipo_habitacion_hotels'] }}" 
                                                                    class="form-check-input" 
                                                                    value="{{ json_encode($habitacion) }}" 
                                                                    {{ $loop->first ? 'checked' : '' }}
                                                                    data-total="{{ $habitacion['Total'] }}" 
                                                                    required>
                                                                <label for="habitacion_{{ $habitacion['Id_tipo_habitacion_hotels'] }}" class="form-check-label">
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
                                            <h5 class="mb-0 me-3"><strong>Total:</strong> $<span id="totalAcumulado{{ $hotel['Id_Hotel'] }}">0.00</span></h5>
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
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Información del Hotel</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="hotel-info-{{ $hotel['Id_Hotel'] }}"></div>
                        <div id="hotel-map" style="width: 100%;"></div>
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

{{-- Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
                // 🔹 1. Script para totales por hotel
        document.querySelectorAll('form').forEach(form => {
            const hotelId = form.querySelector('input[name="Id_Hotel"]').value;
            const radios = form.querySelectorAll('input[type="radio"][name^="habitaciones"]');
            const totalSpan = form.querySelector(`#totalAcumulado${hotelId}`);

            const actualizarTotal = () => {
                let total = 0;
                radios.forEach(radio => {
                    if (radio.checked) {
                        total += parseFloat(radio.getAttribute('data-total') || 0);
                    }
                });
                if (totalSpan) {
                    totalSpan.textContent = total.toFixed(2);
                }
            };

            radios.forEach(r => r.addEventListener('change', () => {
                const grupoIndex = r.name.match(/\d+/)[0];
                const montoGrupo = form.querySelector(`#totalMonto${grupoIndex}`);
                if (montoGrupo) {
                    montoGrupo.textContent = parseFloat(r.dataset.total).toFixed(2);
                }
                actualizarTotal();
            }));

            actualizarTotal(); // Inicializa
        });

        // 🔹 2. Script para los modales e info del hotel

        // Evento para manejar el clic en el enlace "Info"
        document.querySelectorAll('a[data-bs-toggle="modal"]').forEach(function(link) {
            link.addEventListener('click', function(event) {
                // Obtener el ID del hotel desde el enlace
                const hotelId = this.getAttribute('data-bs-target').replace('#hotelModal', '');

                // Hacer una solicitud AJAX para obtener más información del hotel
                fetch(`/hoteles/info/${hotelId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const hotel = data.hotel;
                            const modalBody = document.getElementById(`hotel-info-${hotelId}`);
                            
                            // Construir el contenido ordenado
                            let modalContent = `
                                <h5>${hotel.Nombre_Hotel} ${'⭐'.repeat(hotel.estrellas.estrellas)}</h5>
                                 <p>${hotel.ciudad.Nombre_Ciudad}, ${hotel.pais.Nombre_Pais}</p>
                                <p><strong>Dirección:</strong> ${hotel.Direccion_Hotel}</p>
                                <p><strong>Descripción:</strong> ${hotel.Descripcion_Hotel}</p>

                                <h5>Fotos del Hotel</h5>
                                <div class="gallery">
                                    <img src="${hotel.Foto_Principal_Hotel}" alt="Foto principal de ${hotel.Nombre_Hotel}" class="img-fluid mb-3" style="max-height: 300px; object-fit: cover;">
                                    ${hotel.fotos_hotels.map(foto => `
                                        <img src="${foto.Foto_Hotel}" alt="Foto adicional de ${hotel.Nombre_Hotel}" class="img-fluid mb-3" style="max-height: 200px; object-fit: cover;">
                                    `).join('')}
                                </div>

                                <p><strong>Latitud:</strong> ${hotel.Latitud} <br> <strong>Longitud:</strong> ${hotel.Longitud}</p>

                                <div id="hotel-map" style="height: 400px; width: 100%;"></div>

                                <h5>Facilidades y Servicios</h5>
                                <ul>
                                    ${hotel.hotel_facilidades_y_servicios.map(servicio => `
                                        <li>${servicio.texto_facilidad} - ${servicio.costo} ${servicio.moneda}</li>
                                    `).join('')}
                                </ul>

                                <h5>Tipos de Habitaciones</h5>
                                <ul>
                                    ${hotel.tipo_habitacion_hotels.map(habitacion => `
                                        <li><strong>${habitacion.Nombre_Habitacion}</strong>
                                            <p>Edad Menores Gratis: ${habitacion.Edad_menores_gratis} años</p>
                                        </li>
                                    `).join('')}
                                </ul>
                            `;

                            // Asignar el contenido al cuerpo del modal
                            modalBody.innerHTML = modalContent;

                            // Inicializar el mapa de OpenStreetMap
                            const map = L.map('hotel-map').setView([hotel.Latitud, hotel.Longitud], 13); // Coordenadas del hotel

                            // Añadir la capa del mapa
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                            }).addTo(map);

                            // Añadir un marcador en la ubicación del hotel
                            L.marker([hotel.Latitud, hotel.Longitud]).addTo(map)
                                .bindPopup(`<b>${hotel.Nombre_Hotel}</b><br>${hotel.Direccion_Hotel}`)
                                .openPopup();
                        } else {
                            // En caso de error, mostrar un mensaje en el modal
                            const modalBody = document.getElementById(`hotel-info-${hotelId}`);
                            modalBody.innerHTML = `<p class="text-danger">${data.message}</p>`;
                        }
                    })
                    .catch(error => {
                        console.error('Error al cargar la información del hotel:', error);
                        const modalBody = document.getElementById(`hotel-info-${hotelId}`);
                        modalBody.innerHTML = `<p class="text-danger">Hubo un error al intentar obtener la información del hotel.</p>`;
                    });
            });
        });
    });
</script>

<!-- Incluir CSS de Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>

<!-- Incluir JavaScript de Leaflet -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

@endsection
