@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Reservar un Tour</h2>
    <form id="form-tour" action="{{ route('tours.obtener') }}" method="POST">
        @csrf   
        <!-- Tipo de Servicio -->   
            <input type="hidden" id="Tipo_servicio" name="Tipo_servicio" class="form-control" value="TOU" readonly>


        <!-- Fecha Disponible -->
        <div class="mb-3">
            <label for="Fecha_disponible" class="form-label">Fecha Disponible</label>
            <input type="date" id="Fecha_disponible" name="Fecha_disponible" class="form-control" min="{{ now()->addDay()->format('Y-m-d') }}" max="{{ now()->addDays(365)->format('Y-m-d') }}" onfocus="this.showPicker && this.showPicker()" required>
        </div>

        <!-- Ciudad -->
        <div class="mb-3">
            <label for="Ciudad_Id_Ciudad" class="form-label">Ciudad</label>
            <select id="Ciudad_Id_Ciudad" name="Ciudad_Id_Ciudad" class="form-select select2-ciudades" required>
                <option value="">Selecciona una ciudad</option>
                @foreach($ciudades as $ciudad)
                    <option value="{{ $ciudad->id_ciudad }}"  @if(old('Ciudad_Id_Ciudad') == $ciudad->id_ciudad) selected @endif>{{ $ciudad->nombre_ciudad }} {{ $ciudad->pais->Nombre_Pais }}</option>
                @endforeach
            </select>
        </div>

        <!-- Cantidad de Adultos -->
        <div class="mb-3">
            <label for="Cantidad_adultos" class="form-label">Cantidad de Adultos</label>
            <input type="number" id="Cantidad_adultos" name="Cantidad_adultos" class="form-control" min="1" value="1" required>
        </div>

        <!-- Cantidad de Menores -->
        <div class="mb-3">
            <label for="Cantidad_menores" class="form-label">Cantidad de Menores</label>
            <input type="number" id="Cantidad_menores" name="Cantidad_menores" class="form-control" min="0" value="0" required>
        </div>

        <!-- Edades de Menores -->
        <div id="edadMenoresContainer" class="mb-3" style="display: none;">
            <label class="form-label">Edades de los Menores</label>
            <div id="edadMenoresInputs"></div>
        </div>

        <!-- Botón de Enviar -->
        <button type="submit" class="btn btn-primary">Reservar</button>
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
                edadMenoresContainer.style.display = 'block';

                // Agrega los campos de edad para cada menor
                for (let i = 1; i <= cantidadMenores; i++) {
                    const label = document.createElement('label');
                    label.setAttribute('for', `Edad_menor_${i}`);
                    label.textContent = `Edad del menor ${i}`;
                    label.classList.add('mt-2');

                    const input = document.createElement('input');
                    input.type = 'number';
                    input.id = `Edad_menor_${i}`;
                    input.name = `Edad_menores[${i}]`;
                    input.classList.add('form-control');
                    input.placeholder = `Edad del menor ${i}`;
                    input.min = 1;
                    input.max = 17;
                    input.required = true;

                    // Agrega el label y el input al contenedor
                    edadMenoresInputs.appendChild(label);
                    edadMenoresInputs.appendChild(input);
                }
            } else {
                edadMenoresContainer.style.display = 'none';
            }
        });

        // Manejo del envío del formulario
        document.getElementById('form-tour').addEventListener('submit', function(e) {
            

            // Recopila los datos del formulario
            const TipoServicio = document.getElementById('Tipo_servicio').value;
            const Fecha_disponible = document.getElementById('Fecha_disponible').value;
            const Ciudad_Id_Ciudad = document.getElementById('Ciudad_Id_Ciudad').value;
            const Cantidad_adultos = parseInt(document.getElementById('Cantidad_adultos').value) || 0;
            const Cantidad_menores = parseInt(document.getElementById('Cantidad_menores').value) || 0;

            const edadesMenores = {};
            for (let i = 1; i <= cantidadMenores; i++) {
                const edad = parseInt(document.getElementById(`Edad_menor_${i}`).value) || 0;
                edadesMenores[i] = edad;
            }

            const datosFormulario = {
                Tipo_servicio: Tipo_servicio,
                Fecha_disponible: Fecha_disponible,
                Ciudad_Id_Ciudad: Ciudad_Id_Ciudad,
                Cantidad_adultos: Cantidad_adultos,
                Cantidad_menores: Cantidad_menores,
                Edad_menores: edadesMenores,
            };

            console.log('Datos del formulario:', datosFormulario);
            alert('Reserva realizada con éxito!');
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
            placeholder: "Selecciona una ciudad",
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

