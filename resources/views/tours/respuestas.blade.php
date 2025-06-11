@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Respuestas del Tour</h1>
    @if(isset($respuestas['error']))
        {{$respuestas['error']}}
        <a href="{{ url('/tours') }}" class="btn btn-primary btn-lg mx-2">Tours</a>
    @else
    @php
    //print_r($respuestas);
    @endphp
    <!-- Tabla para mostrar los datos de las respuestas -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Campo</th>
                <th>Valor</th>
                <th>Reservar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($respuestas as $index => $respuesta)
             
                <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                    <td><strong>Nombre Tour</strong></td>
                    <td>
                        {{ $respuesta['Nombre_tour'] }}
                        <a href="#" data-bs-toggle="modal" data-bs-target="#tourModal{{ $respuesta['Id_Tour'] }}" class="ms-2">Info</a>
                    </td>
                    <td rowspan="10">
                        <!-- Formulario de reserva -->
                        <form action="{{ route('tours.addCarrito')}}" method="POST">
                            @csrf
                            <input type="hidden" name="Id_Tour" value="{{ $respuesta['Id_Tour'] }}">
                            <input type="hidden" name="Tipo_servicio" value="TOU">
                            <input type="hidden" name="Id_contrato_tours" value="{{ $respuesta['Id_contrato_tours'] }}">
                            <input type="hidden" name="Fecha_disponible" value="{{ $respuesta['Fecha_disponible'] }}">
                            <input type="hidden" name="Fecha_out" value="{{ $respuesta['Fecha_out'] }}">
                            <input type="hidden" name="Precio_adulto" value="{{ $respuesta['Precio_adulto'] }}">
                            <input type="hidden" name="Precio_menor" value="{{ $respuesta['Precio_menor'] }}">
                            <input type="hidden" name="Numero_adultos" value="{{ $respuesta['Cantidad_adultos'] }}">
                            <input type="hidden" name="Numero_menores" value="{{ $respuesta['Cantidad_menores'] }}">
                            <!-- Verificamos si Edad_menores existe y lo añadimos al formulario -->
                            @if(isset($respuesta['Edad_menores']) && is_array($respuesta['Edad_menores']))
                                <!-- Convertir la matriz Edad_menores a JSON -->
                                <input type="hidden" name="Edad_menores" value="{{ json_encode($respuesta['Edad_menores']) }}">
                            @endif
                            <button type="submit" class="btn btn-success">Reservar</button>
                        </form>
                    </td>
                </tr>
                <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                    <td><strong>Duración del Tour</strong></td>
                    <td>{{ $respuesta['cantidad_dias_tour'] }} Días / {{ $respuesta['cantidad_noches_tour'] }} Noches</td>
                </tr>
                <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                    <td><strong>Precio Total</strong></td>
                    <td>{{ $respuesta['Precio_Total'] }}</td>
                </tr>
                <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                    <td><strong>Fecha Disponible</strong></td>
                    <td>{{ $respuesta['Fecha_disponible'] }}</td>
                </tr>
                <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                    <td><strong>Fecha de Salida</strong></td>
                    <td>{{ $respuesta['Fecha_out'] }}</td>
                </tr>
                <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                    <td><strong>Foto del Tour</strong></td>
                    <td><img src="{{ $respuesta['Foto_tours'] }}" alt="Imagen del Tour" width="100"></td>
                </tr>
                <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                    <td><strong>Adultos / Menores</strong></td>
                    <td>{{ $respuesta['Cantidad_adultos'] }} Adultos / {{ $respuesta['Cantidad_menores'] }} Menores</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">No hay resultados para mostrar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @endif
</div>

<!-- Modales -->
@foreach($respuestas as $respuesta)
<div class="modal fade" id="tourModal{{ isset($respuesta['Id_Tour']) ? $respuesta['Id_Tour'] : 'default' }}" tabindex="-1" aria-labelledby="tourModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tourModalLabel">Información del Tour</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="tour-info-{{ isset($respuesta['Id_Tour']) ? $respuesta['Id_Tour'] : 'default' }}">
                    <p>Cargando...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Capturamos el evento de clic en los enlaces de "Info"
        document.querySelectorAll('a[data-bs-toggle="modal"]').forEach(function (link) {
            link.addEventListener('click', function () {
                const tourId = this.getAttribute('data-bs-target').replace('#tourModal', '');

                // Realizar la solicitud AJAX
                fetch(`/tours/info/${tourId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const tour = data.tour;
                            const modalBody = document.getElementById(`tour-info-${tourId}`);
                            
                            // Construir el contenido del modal
                            let modalContent = `
                            <h5>${tour.Nombre_tour}</h5>
                            <p><strong>Pais:</strong> ${tour.pais.Nombre_Pais}<strong> Ciudad:</strong> ${tour.ciudad.Nombre_Ciudad}<strong> Zona:</strong> ${tour.zona.Nombre_Zona}</p>
                            <h5>Fotos del Tour</h5>
                            <div class="gallery">
                                <img src="${tour.Foto_tours}" alt="Foto principal del tour" class="img-fluid mb-3" style="max-height: 300px; object-fit: cover;">
                                <!-- Aquí agregamos las fotos adicionales si existen -->
                                ${tour.fotos_tours && tour.fotos_tours.length > 0 ? tour.fotos_tours.map(foto => `
                                    <img src="${foto.url_foto_tour}" alt="${foto.nombre_foto_tour}" class="img-fluid mb-3" style="max-height: 300px; object-fit: cover;">
                                `).join('') : ''}
                            </div>                            
                            <p><strong>Recojo del Hotel:</strong> ${tour.Recojo_hotel === 1 ? 'Sí' : 'No'}</p>
                            <p><strong>Punto de Encuentro:</strong> ${tour.Punto_encuentro}</p>
                            <p><strong>Hora Inicio:</strong> ${tour.Horario_inicio}</p>
                            <p><strong>Hora Final:</strong> ${tour.Hora_fin}</p>
                            <p><strong>Dias:</strong> ${tour.cantidad_dias_tour}</p>
                            <p><strong>Noches:</strong> ${tour.cantidad_noches_tour}</p>
                            <p><strong>Descripción:</strong> ${tour.Detalle_tour}</p>
                             <p><strong>Entrega de Agua:</strong> ${tour.Entregan_agua === 1 ? 'Sí' : 'No'}</p>
                            <p><strong>Apto para discapacitados:</strong> ${tour.Para_discapacitados=== 1 ? 'Sí' : 'No'}</p>
                            <p><strong>Con_baño:</strong> ${tour.Con_bano === 1 ? 'Sí' : 'No'}</p>
                        `;
                            
                            // Actualizar el contenido del modal
                            modalBody.innerHTML = modalContent;
                        } else {
                            const modalBody = document.getElementById(`tour-info-${tourId}`);
                            modalBody.innerHTML = `<p class="text-danger">${data.message}</p>`;
                        }
                    })
                    .catch(error => {
                        
                        console.error('Error al cargar la información del tour:', error);
                        const modalBody = document.getElementById(`tour-info-${tourId}`);
                        modalBody.innerHTML = `<p class="text-danger">Hubo un error al intentar obtener la información del tour.</p>`;
                    });
            });
        });
    });
</script>
@endsection
