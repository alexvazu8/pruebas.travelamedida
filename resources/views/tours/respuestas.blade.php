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
    <table class="table table-bordered align-middle">
        <thead class="table-dark text-center">
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
                    <td class="text-end">
                        <!-- Formulario de reserva -->
                        <form action="{{ route('tours.addCarrito')}}" method="POST" class="d-inline">
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
                            @if(isset($respuesta['Edad_menores']) && is_array($respuesta['Edad_menores']))
                                <input type="hidden" name="Edad_menores" value="{{ json_encode($respuesta['Edad_menores']) }}">
                            @endif
                            <button type="submit" class="btn btn-success">Reservar</button>
                        </form>
                    </td>
                </tr>
                <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                    <td><strong>Duración del Tour</strong></td>
                    <td>{{ $respuesta['cantidad_dias_tour'] }} Días / {{ $respuesta['cantidad_noches_tour'] }} Noches</td>
                    <td></td>
                </tr>
                <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                    <td><strong>Precio Total</strong></td>
                    <td>{{ $respuesta['Precio_Total'] }}</td>
                    <td></td>
                </tr>
                <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                    <td><strong>Fecha Disponible</strong></td>
                    <td>{{ $respuesta['Fecha_disponible'] }}</td>
                    <td></td>
                </tr>
                <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                    <td><strong>Fecha de Salida</strong></td>
                    <td>{{ $respuesta['Fecha_out'] }}</td>
                    <td></td>
                </tr>
                <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                    <td><strong>Foto del Tour</strong></td>
                    <td colspan="2" class="text-center">
                        <div class="image-container">
                            <img src="{{ $respuesta['Foto_tours'] }}" 
                                 alt="Imagen del Tour" 
                                 class="img-fluid rounded-3 shadow-lg tour-image">
                        </div>
                    </td>
                </tr>
                <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                    <td><strong>Adultos / Menores</strong></td>
                    <td>{{ $respuesta['Cantidad_adultos'] }} Adultos / {{ $respuesta['Cantidad_menores'] }} Menores</td>
                    <td></td>
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
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title" id="tourModalLabel">Información del Tour</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="tour-info-{{ isset($respuesta['Id_Tour']) ? $respuesta['Id_Tour'] : 'default' }}">
                    <p>Cargando...</p>
                </div>
            </div>
            <div class="modal-footer bg-light rounded-bottom-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
/* 🎨 Estilos visuales mejorados */
.image-container {
    display: flex;
    justify-content: center;
    align-items: center;
}

.tour-image {
    max-height: 350px;
    width: auto;
    border-radius: 1rem;
    box-shadow: 0 4px 10px rgba(0,0,0,0.25);
    transition: transform 0.4s ease, box-shadow 0.4s ease;
}

.tour-image:hover {
    transform: scale(1.12);
    box-shadow: 0 8px 20px rgba(0,0,0,0.35);
}

/* Galería del modal */
.gallery img {
    border-radius: 1rem;
    box-shadow: 0 4px 10px rgba(0,0,0,0.25);
    transition: transform 0.4s ease, box-shadow 0.4s ease;
}

.gallery img:hover {
    transform: scale(1.12);
    box-shadow: 0 8px 20px rgba(0,0,0,0.35);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('a[data-bs-toggle="modal"]').forEach(function (link) {
        link.addEventListener('click', function () {
            const tourId = this.getAttribute('data-bs-target').replace('#tourModal', '');

            fetch(`/tours/info/${tourId}`)
                .then(response => response.json())
                .then(data => {
                    const modalBody = document.getElementById(`tour-info-${tourId}`);
                    if (data.success) {
                        const tour = data.tour;
                        let modalContent = `
                            <h4 class="mb-3 text-center text-primary">${tour.Nombre_tour}</h4>
                            <p><strong>País:</strong> ${tour.pais.Nombre_Pais} 
                               <strong>Ciudad:</strong> ${tour.ciudad.Nombre_Ciudad} 
                               <strong>Zona:</strong> ${tour.zona.Nombre_Zona}</p>
                            <h5 class="mt-4">📸 Fotos del Tour</h5>
                            <div class="gallery d-flex flex-wrap justify-content-center gap-3 mt-3">
                                <img src="${tour.Foto_tours}" alt="Foto principal del tour" 
                                     class="img-fluid mb-3" style="max-height:400px; object-fit:cover;">
                                ${tour.fotos_tours && tour.fotos_tours.length > 0 ? tour.fotos_tours.map(foto => `
                                    <img src="${foto.url_foto_tour}" 
                                         alt="${foto.nombre_foto_tour}" 
                                         class="img-fluid mb-3" 
                                         style="max-height:400px; object-fit:cover;">
                                `).join('') : ''}
                            </div>                            
                            <p><strong>Recojo del Hotel:</strong> ${tour.Recojo_hotel === 1 ? 'Sí' : 'No'}</p>
                            <p><strong>Punto de Encuentro:</strong> ${tour.Punto_encuentro}</p>
                            <p><strong>Hora Inicio:</strong> ${tour.Horario_inicio}</p>
                            <p><strong>Hora Final:</strong> ${tour.Hora_fin}</p>
                            <p><strong>Días:</strong> ${tour.cantidad_dias_tour}</p>
                            <p><strong>Noches:</strong> ${tour.cantidad_noches_tour}</p>
                            <p><strong>Descripción:</strong> ${tour.Detalle_tour}</p>
                            <p><strong>Entrega de Agua:</strong> ${tour.Entregan_agua === 1 ? 'Sí' : 'No'}</p>
                            <p><strong>Apto para discapacitados:</strong> ${tour.Para_discapacitados === 1 ? 'Sí' : 'No'}</p>
                            <p><strong>Con baño:</strong> ${tour.Con_bano === 1 ? 'Sí' : 'No'}</p>
                        `;
                        modalBody.innerHTML = modalContent;
                    } else {
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

