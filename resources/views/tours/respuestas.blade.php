@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4 text-center fw-bold text-primary">{{__("Tours")}}</h1>

    @if(isset($respuestas['error']))
        <div class="alert alert-danger text-center">
            {{$respuestas['error']}}
        </div>
        <div class="text-center">
            <a href="{{ url('/tours') }}" class="btn btn-primary btn-lg mt-2">{{ __("Retornar") }}</a>
        </div>
    @else
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            @forelse($respuestas as $index => $respuesta)
            <div class="tour-card p-4 border-bottom">
                <div class="row g-4 align-items-center">
                    <!-- Columna de imagen -->
                    <div class="col-md-3">
                        <div class="tour-image-container rounded-4 overflow-hidden shadow-sm">
                            <img src="{{ $respuesta['Foto_tours'] }}" 
                                 alt="Imagen del Tour" 
                                 class="tour-image w-100 h-100">
                        </div>
                    </div>
                    
                    <!-- Columna de información -->
                    <div class="col-md-6">
                        <div class="tour-info">
                            <h3 class="tour-title text-primary mb-3">{{ $respuesta['Nombre_tour'] }}</h3>
                            
                            <div class="tour-details">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="detail-item">
                                            <span class="detail-label fw-bold text-secondary">{{__("Duracion")}}:</span>
                                            <span class="detail-value">{{ $respuesta['cantidad_dias_tour'] }} {{__("Dias")}} / {{ $respuesta['cantidad_noches_tour'] }} {{ __("Noches") }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="detail-item">
                                            <span class="detail-label fw-bold text-secondary">{{__("Monto_total")}}:</span>
                                            <span class="detail-value text-success fw-bold">{{ number_format($respuesta['Precio_Total'], 2) }} USD</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="detail-item">
                                            <span class="detail-label fw-bold text-secondary">{{__("Fecha_disponibilidad")}}</span>
                                            <span class="detail-value">
                                                {{__("Ingreso")}}: {{ $respuesta['Fecha_disponible'] }}<br>
                                                {{__("Salida")}}: {{ $respuesta['Fecha_out'] }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="detail-item">
                                            <span class="detail-label fw-bold text-secondary">{{__("Participantes")}}:</span>
                                            <span class="detail-value">
                                                {{ $respuesta['Cantidad_adultos'] }} Adultos / {{ $respuesta['Cantidad_menores'] }} Menores
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Columna de acciones -->
                    <div class="col-md-3">
                        <div class="tour-actions text-center">
                            <form action="{{ route('tours.addCarrito') }}" method="POST" class="mb-3">
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
                                <button type="submit" class="btn btn-success btn-lg w-100 py-2 fw-bold">Reservar Ahora</button>
                            </form>
                            
                            <a href="#" data-bs-toggle="modal" 
                               data-bs-target="#tourModal{{ $respuesta['Id_Tour'] }}" 
                               class="btn btn-outline-primary btn-sm w-100">
                                Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-5">
                <i class="fas fa-search fa-3x mb-3"></i>
                <p class="fs-5">No hay resultados para mostrar.</p>
            </div>
            @endforelse
        </div>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('a[data-bs-toggle="modal"]').forEach(function (link) {
        link.addEventListener('click', function () {
            const tourId = this.getAttribute('data-bs-target').replace('#tourModal', '');
            const modalBody = document.getElementById(`tour-info-${tourId}`);
            modalBody.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="text-muted mt-2">Cargando información...</p>
                </div>
            `;

            fetch(`/tours/info/${tourId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tour = data.tour;
                        modalBody.innerHTML = `
                            <div class="row">
                                <div class="col-md-8">
                                    <h3 class="text-primary fw-bold mb-3">${tour.Nombre_tour}</h3>
                                    <p class="text-muted mb-4">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        ${tour.pais.Nombre_Pais} · ${tour.ciudad.Nombre_Ciudad} · ${tour.zona.Nombre_Zona}
                                    </p>
                                    
                                    <div class="tour-features mb-4">
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                                                    <span><strong>Duración:</strong> ${tour.cantidad_dias_tour} días / ${tour.cantidad_noches_tour} noches</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-clock text-primary me-2"></i>
                                                    <span><strong>Horario:</strong> ${tour.Horario_inicio} - ${tour.Hora_fin}</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-hotel text-primary me-2"></i>
                                                    <span><strong>Recojo del Hotel:</strong> ${tour.Recojo_hotel ? 'Sí' : 'No'}</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-map-pin text-primary me-2"></i>
                                                    <span><strong>Punto de Encuentro:</strong> ${tour.Punto_encuentro}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="tour-description">
                                        <h5 class="fw-bold mb-3">Descripción</h5>
                                        <p class="text-justify">${tour.Detalle_tour}</p>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="additional-info">
                                        <h5 class="fw-bold mb-3">Información Adicional</h5>
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <i class="fas ${tour.Entregan_agua ? 'fa-check text-success' : 'fa-times text-danger'} me-2"></i>
                                                Entrega de Agua
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas ${tour.Para_discapacitados ? 'fa-check text-success' : 'fa-times text-danger'} me-2"></i>
                                                Accesible para Discapacitados
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas ${tour.Con_bano ? 'fa-check text-success' : 'fa-times text-danger'} me-2"></i>
                                                Incluye Baño
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <div class="gallery mt-4">
                                        <img src="${tour.Foto_tours}" alt="Foto principal" class="mb-2">
                                        ${tour.fotos_tours && tour.fotos_tours.length > 0 
                                            ? tour.fotos_tours.map(foto => `<img src="${foto.url_foto_tour}" alt="Foto del tour">`).join('') 
                                            : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        modalBody.innerHTML = `
                            <div class="alert alert-danger text-center">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                ${data.message}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error al cargar la información del tour:', error);
                    modalBody.innerHTML = `
                        <div class="alert alert-danger text-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Hubo un error al obtener la información del tour.
                        </div>
                    `;
                });
        });
    });
});
</script>
@endsection