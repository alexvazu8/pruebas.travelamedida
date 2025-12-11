@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-3">{{ __('Titulo_disponibilidad_traslados') }}</h1>

    <!-- Formulario para obtener la disponibilidad -->
    <form action="{{ route('traslados.obtener') }}" method="POST" class="bg-white p-3 rounded shadow-sm">
        @csrf
        
        <!-- Primera fila: Tipo Servicio, Ciudad, Fecha, Hora -->
        <div class="row g-2 mb-3 pb-2 border-bottom">
            <!-- Tipo Servicio -->
            <div class="form-group col-md-2 col-sm-4">
                <label for="Tipo_servicio_transfer" class="form-label fw-semibold small mb-1">{{ __('Tipo_servicio') }}</label>
                <select name="Tipo_servicio_transfer" id="Tipo_servicio_transfer" class="form-select form-select-sm border-primary-subtle" required>
                    <option value="IN" {{ old('Tipo_servicio_transfer') == 'IN' ? 'selected' : '' }}>🛬 {{ __('Ingreso') }}</option>
                    <option value="OUT" {{ old('Tipo_servicio_transfer') == 'OUT' ? 'selected' : '' }}>🛫 {{ __('Salida') }}</option>
                    <option value="HTH" {{ old('Tipo_servicio_transfer') == 'HTH' ? 'selected' : '' }}>🏨 {{ __('Hotel_hotel') }}</option>
                </select>
                @error('Tipo_servicio_transfer')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <!-- Ciudad -->
            <div class="form-group col-md-3 col-sm-8">
                <label for="Ciudad_Id_Ciudad" class="form-label fw-semibold small mb-1">{{ __('Ciudad') }}</label>
                <select name="Ciudad_Id_Ciudad" id="Ciudad_Id_Ciudad" class="form-select select2-ciudades form-select-sm border-primary-subtle" required>
                    <option value="">Selecciona ciudad</option>
                    @foreach($ciudades as $ciudad)
                        <option value="{{ $ciudad->id_ciudad }}" @if(old('Ciudad_Id_Ciudad') == $ciudad->id_ciudad) selected @endif>
                            {{ $ciudad->nombre_ciudad }}
                        </option>
                    @endforeach
                </select>
                @error('Ciudad_Id_Ciudad')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <!-- Fecha -->
            <div class="form-group col-md-3 col-sm-6">
                <label for="Fecha_disponible" class="form-label fw-semibold small mb-1">{{ __('Fecha_disponibilidad') }}</label>
                <input type="date" id="Fecha_disponible" name="Fecha_disponible" class="form-control form-control-sm border-primary-subtle" value="{{ old('Fecha_disponible') }}" min="{{ now()->addDay()->format('Y-m-d') }}" max="{{ now()->addDays(365)->format('Y-m-d') }}" onfocus="this.showPicker && this.showPicker()" onclick="this.showPicker && this.showPicker()" required>
                @error('Fecha_disponible')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <!-- Hora Servicio -->
            <div class="form-group col-md-2 col-sm-4">
                <label for="hora_servicio" class="form-label fw-semibold small mb-1">{{ __('Hora') }}</label>
                <input type="time" id="hora_servicio" name="hora_servicio" class="form-control form-control-sm border-primary-subtle" value="{{ old('hora_servicio', '09:00') }}" required>
                @error('hora_servicio')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <!-- Botón Buscar (en la primera fila para móviles) -->
            <div class="form-group col-md-2 col-sm-6 d-md-none d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100 py-1">
                    <i class="fas fa-search me-1"></i>{{ __('Buscar') }}
                </button>
            </div>
        </div>

        <!-- Segunda fila: Zona Origen, Zona Destino, Pasajeros -->
        <div class="row g-2 mb-3">
            <!-- Zona Origen -->
            <div class="form-group col-md-3 col-sm-6">
                <label for="Zona_Origen_id" class="form-label fw-semibold small mb-1">{{ __('Origen') }}</label>
                <select name="Zona_Origen_id" id="Zona_Origen_id" class="form-select form-select-sm border-primary-subtle" required>
                    <option value="">{{ __('Origen') }}</option>
                </select>
                @error('Zona_Origen_id')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <!-- Zona Destino -->
            <div class="form-group col-md-3 col-sm-6">
                <label for="Zona_Destino_id" class="form-label fw-semibold small mb-1">{{ __('Destino') }}</label>
                <select name="Zona_Destino_id" id="Zona_Destino_id" class="form-select form-select-sm border-primary-subtle" required>
                    <option value="">{{ __('Destino') }}</option>
                </select>
                @error('Zona_Destino_id')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <!-- Pasajeros -->
            <div class="form-group col-md-4 col-sm-12">
                <label class="form-label fw-semibold small mb-1">{{ __('Pasajeros') }}</label>
                <div class="row g-1">
                    <!-- Adultos -->
                    <div class="col-6 col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light py-1 px-2">
                                <i class="fas fa-user fa-xs"></i>
                            </span>
                            <input type="number" id="Cantidad_adultos" name="Cantidad_adultos" class="form-control py-1" placeholder="Adultos" value="{{ old('Cantidad_adultos', 1) }}" min="1" max="9" required oninput="this.value = Math.min(9, Math.max(1, this.value))">
                        </div>
                        @error('Cantidad_adultos')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Menores -->
                    <div class="col-6 col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light py-1 px-2">
                                <i class="fas fa-child fa-xs"></i>
                            </span>
                            <input type="number" id="Cantidad_menores" name="Cantidad_menores" class="form-control py-1" placeholder="Menores" value="{{ old('Cantidad_menores', 0) }}" min="0" max="5" required oninput="this.value = Math.min(5, this.value)">
                        </div>
                        @error('Cantidad_menores')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Botón para edades (si hay menores) -->
                    <div class="col-12 col-md-2 d-none d-md-block">
                        <button type="button" id="btnEdades" class="btn btn-outline-secondary btn-sm w-100 py-1" style="display: none;">
                            <i class="fas fa-birthday-cake fa-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Botón Buscar (solo en desktop) -->
            <div class="form-group col-md-2 d-none d-md-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100 py-2">
                    <i class="fas fa-search me-1"></i>{{ __('Boton_buscar') }}
                </button>
            </div>
        </div>

        <!-- Edades de menores (se muestra dinámicamente) -->
        <div id="edadMenoresContainer" class="row mb-2" style="display: none;">
            <div class="col-12">
                <label class="form-label fw-semibold small mb-1">
                    <i class="fas fa-birthday-cake me-1"></i>{{ __('Edades') }}
                </label>
                <div id="edadMenoresInputs" class="row g-1"></div>
            </div>
        </div>

        <!-- Botón Buscar (solo móviles, segunda posición) -->
        <div class="row mt-2 d-md-none">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-sm w-100 py-2">
                    <i class="fas fa-search me-2"></i>{{ __('Boton_buscar') }}
                </button>
            </div>
        </div>
    </form>
</div>

<style>
.form-label {
    font-size: 0.8rem;
    margin-bottom: 0.25rem;
}
.form-control, .form-control-sm, .form-select-sm {
    border-radius: 0.25rem;
    transition: all 0.2s ease-in-out;
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
    height: calc(1.5em + 0.5rem + 2px);
}
.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.1);
}
.border-bottom {
    border-color: #e9ecef !important;
}
.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}
.input-group-sm > .form-control,
.input-group-sm > .form-select,
.input-group-sm > .input-group-text {
    padding: 0.15rem 0.3rem;
    font-size: 0.8rem;
    height: calc(1.5em + 0.3rem + 2px);
}
.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}
.fa-xs {
    font-size: 0.75rem;
}
/* Select2 más pequeño */
.select2-container--default .select2-selection--single {
    height: calc(1.5em + 0.5rem + 2px) !important;
    font-size: 0.8rem;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: calc(1.5em + 0.5rem) !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: calc(1.5em + 0.5rem + 2px) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cantidadMenoresInput = document.getElementById('Cantidad_menores');
    const edadMenoresContainer = document.getElementById('edadMenoresContainer');
    const edadMenoresInputs = document.getElementById('edadMenoresInputs');
    const btnEdades = document.getElementById('btnEdades');

    // Actualiza los campos de edad según la cantidad de menores
    cantidadMenoresInput.addEventListener('input', function() {
        const cantidadMenores = parseInt(cantidadMenoresInput.value) || 0;

        // Limpia los campos anteriores
        edadMenoresInputs.innerHTML = '';

        // Si hay menores, muestra los campos para ingresar las edades
        if (cantidadMenores > 0) {
            btnEdades.style.display = 'block';
            btnEdades.innerHTML = `<i class="fas fa-birthday-cake fa-xs"></i> ${cantidadMenores}`;
            
            // Configurar botón para mostrar/ocultar edades
            btnEdades.onclick = function() {
                if (edadMenoresContainer.style.display === 'none') {
                    edadMenoresContainer.style.display = 'block';
                    btnEdades.classList.remove('btn-outline-secondary');
                    btnEdades.classList.add('btn-info');
                } else {
                    edadMenoresContainer.style.display = 'none';
                    btnEdades.classList.remove('btn-info');
                    btnEdades.classList.add('btn-outline-secondary');
                }
            };
            
            // Agrega los campos de edad para cada menor
            for (let i = 1; i <= cantidadMenores; i++) {
                const colDiv = document.createElement('div');
                colDiv.classList.add('col-4', 'col-sm-3', 'col-md-2');
                
                const inputGroup = document.createElement('div');
                inputGroup.classList.add('input-group', 'input-group-sm');
                
                const span = document.createElement('span');
                span.classList.add('input-group-text', 'bg-light', 'py-1');
                span.textContent = `M${i}`;
                
                const input = document.createElement('input');
                input.type = 'number';
                input.id = `Edad_menor_${i}`;
                input.name = `Edad_menores[${i}]`;
                input.classList.add('form-control', 'py-1');
                input.placeholder = 'Edad';
                input.min = "0";
                input.max = "17";
                input.required = true;

                // Crea el contenedor para el error
                const errorContainer = document.createElement('div');
                errorContainer.id = `error_Edad_menor_${i}`;
                errorContainer.classList.add('text-danger', 'small', 'mt-1');
                
                // Agrega los elementos al contenedor
                inputGroup.appendChild(span);
                inputGroup.appendChild(input);
                colDiv.appendChild(inputGroup);
                colDiv.appendChild(errorContainer);
                
                // Agrega la columna al contenedor principal
                edadMenoresInputs.appendChild(colDiv);
            }
        } else {
            btnEdades.style.display = 'none';
            edadMenoresContainer.style.display = 'none';
        }
    });

    const ciudadSelect = document.getElementById('Ciudad_Id_Ciudad');
    const zonaOrigenSelect = document.getElementById('Zona_Origen_id');
    const zonaDestinoSelect = document.getElementById('Zona_Destino_id');
    const tipoServicioSelect = document.getElementById('Tipo_servicio_transfer');

    // Función para cargar zonas
    function cargarZonas() {
        const ciudadId = ciudadSelect.value;
        const tipoServicio = tipoServicioSelect.value;

        if (!ciudadId) {
            zonaOrigenSelect.innerHTML = '<option value="">Selecciona zona</option>';
            zonaDestinoSelect.innerHTML = '<option value="">Selecciona zona</option>';
            return;
        }

        fetch(`/traslados/zonas-origen/${ciudadId}/${tipoServicio}`)
            .then(response => response.json())
            .then(data => { 
                zonaOrigenSelect.innerHTML = '<option value="">Selecciona zona</option>';
                zonaDestinoSelect.innerHTML = '<option value="">Selecciona zona</option>';

                data.zonas.forEach(zona => {
                    const option = document.createElement('option');
                    option.value = zona.Id_Zona;
                    option.textContent = `${zona.nombre_zona}`;
                    zonaOrigenSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error al cargar las zonas:', error);
            });
    }

    // Event listeners
    tipoServicioSelect.addEventListener('change', cargarZonas);
    ciudadSelect.addEventListener('change', cargarZonas);

    // Evento para zona de origen
    zonaOrigenSelect.addEventListener('change', function() {
        const zonaId = this.value;

        if (!zonaId) {
            zonaDestinoSelect.innerHTML = '<option value="">Selecciona zona</option>';
            return;
        }
        
        fetch(`/traslados/zonas-destino/${zonaId}`)
            .then(response => response.json())
            .then(data => {
                zonaDestinoSelect.innerHTML = '<option value="">Selecciona zona</option>';

                data.zonas.forEach(zona => {
                    const option = document.createElement('option');
                    option.value = zona.Id_Zona;
                    option.textContent = ` ${zona.nombre_zona}`;
                    zonaDestinoSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error al cargar las zonas de destino:', error);
            });
    });

    // Trigger inicial para cargar zonas si hay valores pre-seleccionados
    if (ciudadSelect.value) {
        cargarZonas();
    }

    // Dispara el evento input para cargar edades si hay valor inicial
    if (cantidadMenoresInput.value > 0) {
        cantidadMenoresInput.dispatchEvent(new Event('input'));
    }
});
</script>

<!-- jQuery (requerido por Select2) -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
<!-- Select2 JS -->
<script src="{{ asset('js/select2.min.js') }}"></script>

<script>
$(document).ready(function() {
    const $selectCiudades = $('.select2-ciudades');

    $selectCiudades.select2({
        placeholder: "{{ __('Select_ciudad') }}",
        allowClear: true,
        width: '100%',
        dropdownParent: $selectCiudades.parent(),
        minimumResultsForSearch: 3
    });

    // Esto enfoca el campo de búsqueda automáticamente al abrir el select2
    $selectCiudades.on('select2:open', function () {
        let searchField = $('.select2-container--open .select2-search__field');
        if (searchField.length > 0) {
            searchField[0].focus();
        }
    });

    // Manejo del cambio de ciudad con jQuery y Select2
    $('#Ciudad_Id_Ciudad').on('change', function () {
        const ciudadId = $(this).val();
        const tipoServicio = $('#Tipo_servicio_transfer').val();

        if (!ciudadId) {
            $('#Zona_Origen_id').html('<option value="">Selecciona zona</option>');
            $('#Zona_Destino_id').html('<option value="">Selecciona zona</option>');
            return;
        }

        fetch(`/traslados/zonas-origen/${ciudadId}/${tipoServicio}`)
            .then(response => response.json())
            .then(data => {
                $('#Zona_Origen_id').html('<option value="">Selecciona zona</option>');
                $('#Zona_Destino_id').html('<option value="">Selecciona zona</option>');

                data.zonas.forEach(zona => {
                    $('#Zona_Origen_id').append(
                        $('<option>', {
                            value: zona.Id_Zona,
                            text: `${zona.nombre_zona}`
                        })
                    );
                });
            })
            .catch(error => {
                console.error('Error al cargar las zonas:', error);
            });
    });

    $('#Tipo_servicio_transfer').on('change', function () {
        $('#Ciudad_Id_Ciudad').trigger('change');
    });
});
</script>

{{-- Mostrar errores después de que el formulario sea enviado --}}
@foreach(range(1, old('Cantidad_menores', 0)) as $i)
    @error("Edad_menores.$i")
        <span class="text-danger small">{{ $message }}</span>
    @enderror
@endforeach
@endsection