@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">{{ __('Buscar_tour') }}</h2>
    <form id="form-tour" action="{{ route('tours.obtener') }}" method="POST">
        @csrf   
        
        <!-- Tipo de Servicio -->   
        <input type="hidden" id="Tipo_servicio" name="Tipo_servicio" class="form-control" value="TOU" readonly>

        <!-- Fila horizontal para los campos principales -->
        <div class="row g-3 align-items-end">
            <!-- Ciudad -->
            <div class="col-md-3 col-sm-6">
                <label for="Ciudad_Id_Ciudad" class="form-label">{{ __('Ciudad') }}</label>
                <select id="Ciudad_Id_Ciudad" name="Ciudad_Id_Ciudad" class="form-select select2-ciudades" required>
                    <option value="">{{ __('Select_ciudad') }}</option>
                    @foreach($ciudades as $ciudad)
                        <option value="{{ $ciudad->id_ciudad }}"  @if(old('Ciudad_Id_Ciudad') == $ciudad->id_ciudad) selected @endif>
                            {{ $ciudad->nombre_ciudad }} {{ $ciudad->pais->Nombre_Pais }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Fecha Disponible -->
            <div class="col-md-3 col-sm-6">
                <label for="Fecha_disponible" class="form-label">{{ __('Fecha_desde') }}</label>
                <input type="date" id="Fecha_disponible" name="Fecha_disponible" class="form-control form-control-sm w-auto" 
                       min="{{ now()->addDay()->format('Y-m-d') }}" 
                       max="{{ now()->addDays(365)->format('Y-m-d') }}" 
                       onclick="this.showPicker && this.showPicker()" onfocus="this.showPicker && this.showPicker()" required>
            </div>

            <!-- Cantidad de Adultos -->
            <div class="col-md-2 col-sm-4">
                <label for="Cantidad_adultos" class="form-label">{{ __('Cantidad_adultos') }}</label>
                <input type="number" id="Cantidad_adultos" name="Cantidad_adultos" class="form-control" min="1" value="1" required>
            </div>

            <!-- Cantidad de Menores -->
            <div class="col-md-2 col-sm-4">
                <label for="Cantidad_menores" class="form-label">{{ __('Cantidad_menores') }}</label>
                <input type="number" id="Cantidad_menores" name="Cantidad_menores" class="form-control" min="0" value="0" required>
            </div>

            <!-- Botón de Enviar -->
            <div class="col-md-2 col-sm-4">
                <button type="submit" class="btn btn-primary w-100">Reservar</button>
            </div>
        </div>

        <!-- Edades de Menores - Se expande debajo cuando hay menores -->
        <div id="edadMenoresContainer" class="row g-3 mt-3" style="display: none;">
            <div class="col-12">
                <label class="form-label fw-bold">{{ __('Edades_menores') }}</label>
            </div>
            <div class="row g-2" id="edadMenoresInputs"></div>
        </div>
    </form>
</div>

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
                edadMenoresContainer.style.display = 'flex';

                // Agrega los campos de edad para cada menor
                for (let i = 1; i <= cantidadMenores; i++) {
                    const colDiv = document.createElement('div');
                    colDiv.classList.add('col-md-2', 'col-sm-4', 'col-6');

                    const label = document.createElement('label');
                    label.setAttribute('for', `Edad_menor_${i}`);
                    label.textContent = `Menor ${i}`;
                    label.classList.add('form-label', 'small');

                    const input = document.createElement('input');
                    input.type = 'number';
                    input.id = `Edad_menor_${i}`;
                    input.name = `Edad_menores[${i}]`;
                    input.classList.add('form-control');
                    input.placeholder = `Edad`;
                    input.min = 1;
                    input.max = 17;
                    input.required = true;

                    // Agrega el label y el input al contenedor
                    colDiv.appendChild(label);
                    colDiv.appendChild(input);
                    edadMenoresInputs.appendChild(colDiv);
                }
            } else {
                edadMenoresContainer.style.display = 'none';
            }
        });

        // Manejo del envío del formulario - VERSIÓN CORREGIDA
        document.getElementById('form-tour').addEventListener('submit', function(e) {
            // Obtener la cantidad de menores actual
            const cantidadMenores = parseInt(document.getElementById('Cantidad_menores').value) || 0;

            // Recopila los datos del formulario
            const TipoServicio = document.getElementById('Tipo_servicio').value;
            const Fecha_disponible = document.getElementById('Fecha_disponible').value;
            const Ciudad_Id_Ciudad = document.getElementById('Ciudad_Id_Ciudad').value;
            const Cantidad_adultos = parseInt(document.getElementById('Cantidad_adultos').value) || 0;
            const Cantidad_menores = cantidadMenores;

            const edadesMenores = {};
            for (let i = 1; i <= cantidadMenores; i++) {
                const edadInput = document.getElementById(`Edad_menor_${i}`);
                if (edadInput) {
                    const edad = parseInt(edadInput.value) || 0;
                    edadesMenores[i] = edad;
                }
            }

            const datosFormulario = {
                Tipo_servicio: TipoServicio,
                Fecha_disponible: Fecha_disponible,
                Ciudad_Id_Ciudad: Ciudad_Id_Ciudad,
                Cantidad_adultos: Cantidad_adultos,
                Cantidad_menores: Cantidad_menores,
                Edad_menores: edadesMenores,
            };

            console.log('Datos del formulario:', datosFormulario);
            //alert('Reserva realizada con éxito!');
            
            // EL FORMULARIO SE ENVIARÁ AUTOMÁTICAMENTE DESPUÉS DEL ALERT
            // No necesitas hacer nada más
        });
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