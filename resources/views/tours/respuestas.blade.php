@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4 text-center fw-bold text-primary">Respuestas del Tour</h1>

    @if(isset($respuestas['error']))
        <div class="alert alert-danger text-center">
            {{$respuestas['error']}}
        </div>
        <div class="text-center">
            <a href="{{ url('/tours') }}" class="btn btn-primary btn-lg mt-2">Volver a Tours</a>
        </div>
    @else
    <div class="card shadow-lg border-0 rounded-4 p-3">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="text-center">
                    <th>Campo</th>
                    <th>Valor</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($respuestas as $index => $respuesta)
                    <tr>
                        <td class="fw-bold text-secondary">Nombre del Tour</td>
                        <td>
                            <span class="fw-semibold">{{ $respuesta['Nombre_tour'] }}</span>
                            <a href="#" data-bs-toggle="modal" 
                               data-bs-target="#tourModal{{ $respuesta['Id_Tour'] }}" 
                               class="badge bg-info text-dark ms-2 text-decoration-none">Ver Info</a>
                        </td>
                        <td class="text-end">
                            <form action="{{ route('tours.addCarrito') }}" method="POST" class="d-inline">
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
                                <button type="submit" class="btn btn-success btn-sm px-3">Reservar</button>
                            </form>
                        </td>
                    </tr>

                    <tr>
                        <td class="fw-bold text-secondary">Duración</td>
                        <td>{{ $respuesta['cantidad_dias_tour'] }} Días / {{ $respuesta['cantidad_noches_tour'] }} Noches</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="fw-bold text-secondary">Precio Total</td>
                        <td>${{ number_format($respuesta['Precio_Total'], 2) }}</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="fw-bold text-secondary">Fechas</td>
                        <td>Disponible: {{ $respuesta['Fecha_disponible'] }} | Salida: {{ $respuesta['Fecha_out'] }}</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="fw-bold text-secondary">Foto del Tour</td>
                        <td colspan="2" class="text-center">
                            <div class="tour-thumb mx-auto">
                                <img src="{{ $respuesta['Foto_tours'] }}" 
                                     alt="Imagen del Tour" 
                                     class="rounded-4 shadow-sm tour-image">
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="fw-bold text-secondary">Participantes</td>
                        <td>{{ $respuesta['Cantidad_adultos'] }} Adultos / {{ $respuesta['Cantidad_menores'] }} Menores</td>
                        <td></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-3">No hay resultados para mostrar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif
</div>

<!-- MODALES DE INFORMACIÓN -->
@foreach($respuestas as $respuesta)
<div class="modal fade" id="tourModal{{ isset($respuesta['Id_Tour']) ? $respuesta['Id_Tour'] : 'default' }}" tabindex="-1" aria-labelledby="tourModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-semibold" id="tourModalLabel">Información del Tour</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="tour-info-{{ $respuesta['Id_Tour'] }}">
                <p class="text-center text-muted">Cargando información...</p>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
/* 🎨 UX/UI Mejorado */
.tour-thumb {
    width: 280px;
    height: 180px;
    overflow: hidden;
    border-radius: 1rem;
}

.tour-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .5s ease, box-shadow .4s ease;
}

.tour-image:hover {
    transform: scale(1.08);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

/* Galería en modal */
.gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.gallery img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 0.8rem;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
    transition: transform .4s ease, box-shadow .4s ease;
}

.gallery img:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('a[data-bs-toggle="modal"]').forEach(function (link) {
        link.addEventListener('click', function () {
            const tourId = this.getAttribute('data-bs-target').replace('#tourModal', '');
            const modalBody = document.getElementById(`tour-info-${tourId}`);
            modalBody.innerHTML = `<p class="text-center text-muted">Cargando información...</p>`;

            fetch(`/tours/info/${tourId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tour = data.tour;
                        modalBody.innerHTML = `
                            <h3 class="text-center text-primary fw-bold mb-3">${tour.Nombre_tour}</h3>
                            <p class="text-center text-muted">
                                ${tour.pais.Nombre_Pais} · ${tour.ciudad.Nombre_Ciudad} · ${tour.zona.Nombre_Zona}
                            </p>
                            <div class="gallery">
                                <img src="${tour.Foto_tours}" alt="Foto principal">
                                ${tour.fotos_tours && tour.fotos_tours.length > 0 
                                    ? tour.fotos_tours.map(foto => `<img src="${foto.url_foto_tour}" alt="${foto.nombre_foto_tour}">`).join('') 
                                    : ''}
                            </div>
                            <div class="mt-4">
                                <p><strong>Recojo del Hotel:</strong> ${tour.Recojo_hotel ? 'Sí' : 'No'}</p>
                                <p><strong>Punto de Encuentro:</strong> ${tour.Punto_encuentro}</p>
                                <p><strong>Horario:</strong> ${tour.Horario_inicio} - ${tour.Hora_fin}</p>
                                <p><strong>Duración:</strong> ${tour.cantidad_dias_tour} días / ${tour.cantidad_noches_tour} noches</p>
                                <p><strong>Descripción:</strong> ${tour.Detalle_tour}</p>
                                <p><strong>Entrega de Agua:</strong> ${tour.Entregan_agua ? 'Sí' : 'No'}</p>
                                <p><strong>Accesible para Discapacitados:</strong> ${tour.Para_discapacitados ? 'Sí' : 'No'}</p>
                                <p><strong>Incluye Baño:</strong> ${tour.Con_bano ? 'Sí' : 'No'}</p>
                            </div>
                        `;
                    } else {
                        modalBody.innerHTML = `<p class="text-danger text-center">${data.message}</p>`;
                    }
                })
                .catch(error => {
                    console.error('Error al cargar la información del tour:', error);
                    modalBody.innerHTML = `<p class="text-danger text-center">Hubo un error al obtener la información del tour.</p>`;
                });
        });
    });
});
</script>
@endsection
