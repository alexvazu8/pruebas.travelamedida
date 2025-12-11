@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">{{ __('Titulo_disponibilidad_traslados') }}</h1>

    <!-- Formulario para obtener la disponibilidad -->
    <form action="{{ route('traslados.obtener') }}" method="POST" class="bg-white p-4 rounded shadow-sm">
        @csrf
        
        <!-- Primera fila: Tipo Servicio, Ciudad, Fecha, Hora -->
        <div class="row mb-4 pb-3 border-bottom">
            <!-- Tipo Servicio -->
            <div class="form-group col-md-3 col-sm-6 mb-3">
                <label for="Tipo_servicio_transfer" class="form-label fw-semibold small">{{ __('Tipo_servicio') }}</label>
                <select name="Tipo_servicio_transfer" id="Tipo_servicio_transfer" class="form-control form-control-sm border-primary-subtle" required>
                    <option value="IN" {{ old('Tipo_servicio_transfer') == 'IN' ? 'selected' : '' }}>🛬 {{ __('Ingreso') }}</option>
                    <option value="OUT" {{ old('Tipo_servicio_transfer') == 'OUT' ? 'selected' : '' }}>🛫 {{ __('Salida') }}</option>
                    <option value="HTH" {{ old('Tipo_servicio_transfer') == 'HTH' ? 'selected' : '' }}>🏨{{ __('Hotel_hotel') }}</option>
                </select>
                @error('Tipo_servicio_transfer')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <!-- Ciudad -->
            <div class="form-group col-md-3 col-sm-6 mb-3">
                <label for="Ciudad_Id_Ciudad" class="form-label fw-semibold small">{{ __('Ciudad') }}</label>
                <select name="Ciudad_Id_Ciudad" id="Ciudad_Id_Ciudad" class="form-control select2-ciudades form-control-sm border-primary-subtle" required>
                    <option value="">Selecciona una ciudad</option>
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
            <div class="form-group col-md-3 col-sm-6 mb-3">
                <label for="Fecha_disponible" class="form-label fw-semibold small">{{ __('Fecha_disponibilidad') }}</label>
                <input type="date" id="Fecha_disponible" name="Fecha_disponible" class="form-control form-control-sm border-primary-subtle" value="{{ old('Fecha_disponible') }}" min="{{ now()->addDay()->format('Y-m-d') }}" max="{{ now()->addDays(365)->format('Y-m-d') }}" onfocus="this.showPicker && this.showPicker()" onclick="this.showPicker && this.showPicker()" required>
                @error('Fecha_disponible')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <!-- Hora Servicio -->
            <div class="form-group col-md-3 col-sm-6 mb-3">
                <label for="hora_servicio" class="form-label fw-semibold small">{{ __('Hora_servicio') }}</label>
                <input type="time" id="hora_servicio" name="hora_servicio" class="form-control form-control-sm border-primary-subtle" value="{{ old('hora_servicio') }}" required>
                @error('hora_servicio')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Segunda fila: Zona Origen, Zona Destino, Pasajeros -->
        <div class="row mb-4">
            <!-- Zona Origen -->
            <div class="form-group col-md-4 col-sm-6 mb-3">
                <label for="Zona_Origen_id" class="form-label fw-semibold small">{{ __('Zona_origen') }}</label>
                <select name="Zona_Origen_id" id="Zona_Origen_id" class="form-control form-control-sm border-primary-subtle" required>
                    <option value="">{{ __('Zona_origen') }}</option>
                </select>
                @error('Zona_Origen_id')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <!-- Zona Destino -->
            <div class="form-group col-md-4 col-sm-6 mb-3">
                <label for="Zona_Destino_id" class="form-label fw-semibold small">{{ __('Zona_destino') }}</label>
                <select name="Zona_Destino_id" id="Zona_Destino_id" class="form-control form-control-sm border-primary-subtle" required>
                    <option value="">{{ __('Zona_destino') }}</option>
                </select>
                @error('Zona_Destino_id')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <!-- Pasajeros -->
            <div class="form-group col-md-4 mb-3">
                <label class="form-label fw-semibold small">{{ __('Titulo_pasajeros') }}</label>
                <div class="row g-2">
                    <!-- Adultos -->
                    <div class="col-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="number" id="Cantidad_adultos" name="Cantidad_adultos" class="form-control" placeholder="Adultos" value="{{ old('Cantidad_adultos', 1) }}" min="1" max="9" required oninput="this.value = Math.min(9, Math.max(1, this.value))">
                        </div>
                        @error('Cantidad_adultos')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Menores -->
                    <div class="col-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-child"></i>
                            </span>
                            <input type="number" id="Cantidad_menores" name="Cantidad_menores" class="form-control" placeholder="Menores" value="{{ old('Cantidad_menores', 0) }}" min="0" max="5" required oninput="this.value = Math.min(5, this.value)">
                        </div>
                        @error('Cantidad_menores')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Edades de menores (se muestra dinámicamente) -->
        <div id="edadMenoresContainer" class="row mb-3" style="display: none;">
            <div class="col-12">
                <label class="form-label fw-semibold small">
                    <i class="fas fa-birthday-cake me-1"></i>{{ __('Edades_menores') }}
                </label>
                <div id="edadMenoresInputs" class="row g-2"></div>
            </div>
        </div>

        <!-- Botón Buscar -->
        <div class="row mt-3">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-sm w-100 py-2 fw-semibold">
                    <i class="fas fa-search me-2"></i>{{ __('Boton_buscar') }}
                </button>
            </div>
        </div>
    </form>
</div>

<style>
.form-label {
    font-size: 0.85rem;
    margin-bottom: 0.3rem;
}
.form-control, .form-control-sm {
    border-radius: 0.375rem;
    transition: all 0.2s ease-in-out;
    font-size: 0.875rem;
}
.form-control:focus, .form-control-sm:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
}
.border-bottom {
    border-color: #e9ecef !important;
}
.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}
.input-group-sm > .form-control,
.input-group-sm > .input-group-text {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cantidadMenoresInput = document.getElementById('Cantidad_menores');
    const edadMenoresContainer = document.getElementById('edadMenoresContainer');
    const edadMenoresInputs = document.getElementById('edadMenoresInputs');

    // Actualiza los campos de edad según la cantidad de menores
    cantidadMenoresInput.addEventListener('input', function() {
        const cantidadMenores = parseInt(cantidadMenoresInput.value) || 0;

        // Limpia los campos anteriores
        edadMenoresInputs.innerHTML = '';

        // Si hay menores, muestra los campos para ingresar las edades
        if (cantidadMenores > 0) {
            edadMenoresContainer.style.display = 'block';

            // Agrega los campos de edad para cada menor
            for (let i = 1; i <= cantidadMenores; i++) {
                const colDiv = document.createElement('div');
                colDiv.classList.add('col-6', 'col-sm-4', 'col-md-3');
                
                const inputGroup = document.createElement('div');
                inputGroup.classList.add('input-group', 'input-group-sm');
                
                const span = document.createElement('span');
                span.classList.add('input-group-text', 'bg-light');
                span.textContent = `M${i}`;
                
                const input = document.createElement('input');
                input.type = 'number';
                input.id = `Edad_menor_${i}`;
                input.name = `Edad_menores[${i}]`;
                input.classList.add('form-control');
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
            zonaOrigenSelect.innerHTML = '<option value="">Selecciona una zona</option>';
            zonaDestinoSelect.innerHTML = '<option value="">Selecciona una zona</option>';
            return;
        }

        fetch(`/traslados/zonas-origen/${ciudadId}/${tipoServicio}`)
            .then(response => response.json())
            .then(data => { 
                zonaOrigenSelect.innerHTML = '<option value="">Selecciona una zona</option>';
                zonaDestinoSelect.innerHTML = '<option value="">Selecciona una zona</option>';

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
            zonaDestinoSelect.innerHTML = '<option value="">Selecciona una zona</option>';
            return;
        }
        
        fetch(`/traslados/zonas-destino/${zonaId}`)
            .then(response => response.json())
            .then(data => {
                zonaDestinoSelect.innerHTML = '<option value="">Selecciona una zona</option>';

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
        dropdownParent: $selectCiudades.parent()
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
            $('#Zona_Origen_id').html('<option value="">Selecciona una zona</option>');
            $('#Zona_Destino_id').html('<option value="">Selecciona una zona</option>');
            return;
        }

        fetch(`/traslados/zonas-origen/${ciudadId}/${tipoServicio}`)
            .then(response => response.json())
            .then(data => {
                $('#Zona_Origen_id').html('<option value="">Selecciona una zona</option>');
                $('#Zona_Destino_id').html('<option value="">Selecciona una zona</option>');

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