@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4 text-center fw-bold text-primary">Respuestas del Tour</h1>

    @if(isset($respuestas['error']))
        <div class="alert alert-danger text-center">{{ $respuestas['error'] }}</div>
        <div class="text-center">
            <a href="{{ url('/tours') }}" class="btn btn-primary btn-lg">Volver a Tours</a>
        </div>
    @else
        <div class="table-responsive shadow-sm rounded-4 overflow-hidden">
            <table class="table table-hover align-middle mb-0">
                <tbody>
                    @foreach ($respuestas as $index => $respuesta)
                    <tr class="{{ $index % 2 == 0 ? 'table-light' : 'table-dark text-white' }}">
                        <td class="fw-semibold text-center" style="width: 25%;">Foto del Tour</td>
                        <td class="text-center">
                            <div class="image-container mx-auto">
                                <img src="{{ $respuesta['Foto_tours'] }}"
                                     alt="Imagen del Tour"
                                     class="tour-img">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-center py-3">
                            <button class="btn btn-outline-primary btn-lg view-tour-btn"
                                    data-id="{{ $respuesta['id_tour'] }}">
                                Ver detalles del tour
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Modal -->
<div class="modal fade" id="tourModal" tabindex="-1" aria-labelledby="tourModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content rounded-4 shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="tourModalLabel">Detalles del Tour</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-4" id="modalBody">
        <p class="text-center text-muted">Cargando información...</p>
      </div>
    </div>
  </div>
</div>

<style>
    /* === GALERÍA PRINCIPAL === */
    .image-container {
        width: 300px;
        height: 200px;
        overflow: hidden;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .tour-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .image-container:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    }

    .image-container:hover .tour-img {
        transform: scale(1.1);
    }

    /* === GALERÍA EN EL MODAL === */
    .gallery {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1.5rem;
    }

    .gallery-item {
        width: 280px;
        height: 190px;
        overflow: hidden;
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .gallery-item:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    }

    .gallery-item:hover img {
        transform: scale(1.1);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.view-tour-btn').forEach(button => {
        button.addEventListener('click', async () => {
            const tourId = button.dataset.id;
            const modal = new bootstrap.Modal(document.getElementById('tourModal'));
            const modalBody = document.getElementById('modalBody');
            modalBody.innerHTML = '<p class="text-center text-muted">Cargando información...</p>';

            try {
                const response = await fetch(`/tours/${tourId}`);
                const tour = await response.json();

                let modalContent = `
                    <h3 class="fw-bold mb-3 text-center text-primary">${tour.Nombre_tour}</h3>
                    <p class="text-center mb-4"><strong>${tour.pais.Nombre_Pais}</strong> • ${tour.ciudad.Nombre_Ciudad} • ${tour.zona.Nombre_Zona}</p>

                    <div class="gallery">
                        <div class="gallery-item">
                            <img src="${tour.Foto_tours}" alt="Foto principal del tour">
                        </div>
                        ${tour.fotos_tours && tour.fotos_tours.length > 0 ? tour.fotos_tours.map(foto => `
                            <div class="gallery-item">
                                <img src="${foto.url_foto_tour}" alt="${foto.nombre_foto_tour}">
                            </div>
                        `).join('') : ''}
                    </div>

                    <div class="mt-4">
                        <p><strong>Recojo del Hotel:</strong> ${tour.Recojo_hotel ? 'Sí' : 'No'}</p>
                        <p><strong>Punto de Encuentro:</strong> ${tour.Punto_encuentro}</p>
                        <p><strong>Hora Inicio:</strong> ${tour.Horario_inicio}</p>
                        <p><strong>Hora Final:</strong> ${tour.Hora_fin}</p>
                        <p><strong>Días:</strong> ${tour.cantidad_dias_tour}</p>
                        <p><strong>Noches:</strong> ${tour.cantidad_noches_tour}</p>
                        <p><strong>Descripción:</strong> ${tour.Detalle_tour}</p>
                        <p><strong>Entrega de Agua:</strong> ${tour.Entregan_agua ? 'Sí' : 'No'}</p>
                        <p><strong>Apto para Discapacitados:</strong> ${tour.Para_discapacitados ? 'Sí' : 'No'}</p>
                        <p><strong>Con baño:</strong> ${tour.Con_bano ? 'Sí' : 'No'}</p>
                    </div>
                `;
                modalBody.innerHTML = modalContent;
                modal.show();
            } catch (error) {
                console.error(error);
                modalBody.innerHTML = '<p class="text-danger text-center">Error al cargar los detalles del tour.</p>';
            }
        });
    });
});
</script>
@endsection
