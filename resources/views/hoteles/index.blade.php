@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('Titulo_disponibilidad_hoteles') }}</h1>
    <form action="{{ route('hoteles.obtener') }}" method="POST">
        @csrf
        
        <div class="row">
            <!-- Ciudad del Hotel -->
            <div class="form-group col-md-3">
                <label for="Id_Ciudad_Hotel">{{ __('Ciudad_hotel') }}</label>
                <select class="form-control select2-ciudades" id="Id_Ciudad_Hotel" name="Id_Ciudad_Hotel" required>
                    <option value="">Selecciona una ciudad</option>
                    @foreach($ciudades as $ciudad)
                        <option value="{{ $ciudad->id_ciudad }}"
                            @if(old('Id_Ciudad_Hotel') == $ciudad->id_ciudad) selected @endif>
                            {{ $ciudad->nombre_ciudad }} {{ $ciudad->pais->Nombre_Pais }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Fecha Desde -->
            <div class="form-group col-md-2">
                <label for="Fecha_desde">{{ __('Fecha_desde') }}</label>
                <input type="date" class="form-control" id="Fecha_desde" name="Fecha_desde" min="{{ now()->format('Y-m-d') }}" max="{{ now()->addDays(365)->format('Y-m-d') }}" onfocus="this.showPicker && this.showPicker()" onclick="this.showPicker && this.showPicker()" required onchange="const nextDay = new Date(this.value); nextDay.setDate(nextDay.getDate() + 1); document.getElementById('Fecha_hasta').min = nextDay.toISOString().split('T')[0]; document.getElementById('Fecha_hasta').value = nextDay.toISOString().split('T')[0]">
            </div>

            <!-- Fecha Hasta -->
            <div class="form-group col-md-2">
                <label for="Fecha_hasta">{{ __('Fecha_hasta') }}</label>
                <input type="date" class="form-control" id="Fecha_hasta" name="Fecha_hasta" min="{{ now()->addDay()->format('Y-m-d') }}" max="{{ now()->addDays(365)->format('Y-m-d') }}" onfocus="this.showPicker && this.showPicker()" onclick="this.showPicker && this.showPicker()" required>
            </div>

            <!-- Número de Habitaciones -->
            <div class="form-group col-md-2">
                <label for="Numero_Habitaciones">{{ __('Numero_habitaciones') }}</label>
                <input type="number" class="form-control" id="Numero_Habitaciones" name="Numero_Habitaciones" min="1" max="3" value="1" required>
            </div>

            <!-- Botón de Buscar -->
            <div class="form-group col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">{{ __('Search') }}</button>
            </div>
        </div>

        <!-- Contenedor dinámico de habitaciones -->
        <div id="habitaciones-container" class="mt-4">
            <!-- Se generan dinámicamente habitaciones aquí -->
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const habitacionesContainer = document.getElementById('habitaciones-container');
        const habitacionesInput = document.getElementById('Numero_Habitaciones');

        function crearHabitacion(habitacionId) {
            const habitacionDiv = document.createElement('div');
            habitacionDiv.classList.add('habitacion', 'mb-4', 'p-3', 'border', 'rounded');
            habitacionDiv.id = `habitacion_${habitacionId}`;
            habitacionDiv.innerHTML = `
                <h5>{{ __('Habitacion') }} ${habitacionId}</h5>
                <div class="row">
                    <!-- Cantidad de Adultos -->
                    <div class="form-group col-md-6">
                        <label for="Cantidad_adultos_${habitacionId}">{{ __('Cantidad_adultos') }}</label>
                        <input type="number" class="form-control" id="Cantidad_adultos_${habitacionId}" name="habitaciones[${habitacionId}][Cantidad_adultos]" min="1" max="4" value="1" required>
                    </div>

                    <!-- Cantidad de Menores -->
                    <div class="form-group col-md-6">
                        <label for="Cantidad_menores_${habitacionId}">{{ __('Cantidad_menores') }}</label>
                        <input type="number" class="form-control cantidad-menores" id="Cantidad_menores_${habitacionId}" name="habitaciones[${habitacionId}][Cantidad_menores]" min="0" max="4" value="0" required>
                    </div>
                </div>

                <!-- Edad de los Menores -->
                <div class="edades-menores-container" id="edades_menores_${habitacionId}">
                    <!-- Campos dinámicos de edades -->
                </div>
            `;
            habitacionesContainer.appendChild(habitacionDiv);
        }

        function actualizarHabitaciones() {
            habitacionesContainer.innerHTML = '';
            const numeroHabitaciones = parseInt(habitacionesInput.value) || 1;
            
            for (let i = 1; i <= Math.min(numeroHabitaciones, 3); i++) {
                crearHabitacion(i);
            }
        }

        function actualizarEdadesMenores(habitacionId) {
            const cantidadMenoresInput = document.getElementById(`Cantidad_menores_${habitacionId}`);
            const edadesMenoresContainer = document.getElementById(`edades_menores_${habitacionId}`);
            
            let cantidadMenores = parseInt(cantidadMenoresInput.value) || 0;

            // Limitar a un máximo de 4 menores
            if (cantidadMenores > 4) {
                cantidadMenores = 4;
                cantidadMenoresInput.value = 4;
            }

            edadesMenoresContainer.innerHTML = '';
            
            if (cantidadMenores > 0) {
                const edadesRow = document.createElement('div');
                edadesRow.classList.add('row');
                edadesRow.innerHTML = '<div class="col-12"><label class="mb-2">{{ __('Edades_menores') }}:</label></div>';
                
                for (let i = 1; i <= cantidadMenores; i++) {
                    const edadCol = document.createElement('div');
                    edadCol.classList.add('form-group', 'col-md-3', 'col-sm-6');
                    edadCol.innerHTML = `
                        <label for="Edad_menor_${habitacionId}_${i}">{{ __('Edad_menor') }} ${i}</label>
                        <input type="number" class="form-control" id="Edad_menor_${habitacionId}_${i}" name="habitaciones[${habitacionId}][Edad_menores][${i}]" min="0" max="17" required>
                    `;
                    edadesRow.appendChild(edadCol);
                }
                edadesMenoresContainer.appendChild(edadesRow);
            }
        }

        habitacionesInput.addEventListener('input', actualizarHabitaciones);

        habitacionesContainer.addEventListener('input', function (e) {
            if (e.target.classList.contains('cantidad-menores')) {
                const habitacionId = e.target.id.split('_')[2];
                actualizarEdadesMenores(habitacionId);
            }
        });

        // Inicializar con una habitación por defecto
        actualizarHabitaciones();
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
            width: '100%'
        });

        // Esto enfoca el campo de búsqueda automáticamente al abrir el select2
        $selectCiudades.on('select2:open', function () {
            let searchField = $('.select2-container--open .select2-search__field');
            if (searchField.length > 0) {
                searchField[0].focus();
            }
        });
    });
</script>
@endsection