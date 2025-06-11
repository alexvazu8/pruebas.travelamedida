@extends('layouts.app')

@section('content')

    <div class="container">
        <h1>Disponibilidad de Traslados</h1>

        <!-- Formulario para obtener la disponibilidad -->
        <form action="{{ route('traslados.obtener') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="Fecha_disponible">Fecha de Disponibilidad</label>
                <input type="date"  id="Fecha_disponible" name="Fecha_disponible" class="form-control  bg-light text-dark"  value="{{ old('Fecha_disponible') }}" min="{{ now()->addDay()->format('Y-m-d') }}" max="{{ now()->addDays(365)->format('Y-m-d') }}"  onfocus="this.showPicker && this.showPicker()" required>
                @error('Fecha_disponible')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="Tipo_servicio_transfer">IN/OUT/Hotel a Hotel</label>
                <select name="Tipo_servicio_transfer" id="Tipo_servicio_transfer" class="form-control" required>
                    <option value="IN" {{ old('Tipo_servicio_transfer') == 'IN' ? 'selected' : '' }}>Ingreso</option>
                    <option value="OUT" {{ old('Tipo_servicio_transfer') == 'OUT' ? 'selected' : '' }}>Salida</option>
                    <option value="HTH" {{ old('Tipo_servicio_transfer') == 'HTH' ? 'selected' : '' }}>De Hotel a Hotel</option>
                </select>
                @error('Tipo_servicio_transfer')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
<!-- Este es un comentario HTML, no se procesará pero será visible en el inspector 
            <div class="form-group">
                <label for="Pais_Id_Pais">Pais</label>
                <select name="Pais_Id_Pais" id="Pais_Id_Pais" class="form-control" required>
                @foreach($paises as $paise)    
                <option value="{{$paise->Id_Pais}}" @if(old('Pais_Id_Pais') == $paise->Id_Pais) selected @endif>{{$paise->Nombre_Pais}}</option>
                @endforeach 
                </select>
                @error('Pais_Id_Pais')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            Fin del comentario de Pais-->

            <div class="form-group">
                <label for="Ciudad_Id_Ciudad">Ciudad</label>
                <select name="Ciudad_Id_Ciudad" id="Ciudad_Id_Ciudad" class="form-control select2-ciudades" required>
                <option value="">Selecciona una ciudad</option>
                @foreach($ciudades as $ciudad)
                    <option value="{{ $ciudad->id_ciudad }}"  @if(old('Ciudad_Id_Ciudad') == $ciudad->id_ciudad) selected @endif>{{ $ciudad->nombre_ciudad }} {{ $ciudad->pais->Nombre_Pais }}</option>
                @endforeach
                    
                </select>
                @error('Ciudad_Id_Ciudad')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Zona Origen -->
            <div class="form-group">
                <label for="Zona_Origen_id">Zona de Origen</label>
                <select name="Zona_Origen_id" id="Zona_Origen_id" class="form-control" required>
                    <option value="">Selecciona una zona</option>
                </select>
                @error('Zona_Origen_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="Zona_Destino_id">Zona de Destino</label>
                <select name="Zona_Destino_id" id="Zona_Destino_id" class="form-control" required>
                    <option value="">Selecciona una zona destino</option>
                </select>
                @error('Zona_Destino_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="hora_servicio">Hora de Servicio</label>
                <input type="time" id="hora_servicio" name="hora_servicio" class="form-control" value="{{ old('hora_servicio') }}" required>
                @error('hora_servicio')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="Cantidad_adultos">Cantidad de Adultos</label>
                <input type="number" id="Cantidad_adultos" name="Cantidad_adultos" class="form-control" value="{{ old('Cantidad_adultos') }}" min="0" max="9" required  oninput="this.value = Math.min(9, this.value)">
                @error('Cantidad_adultos')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="Cantidad_menores">Cantidad de Menores</label>
                <input type="number" id="Cantidad_menores" name="Cantidad_menores" class="form-control" min="0" max="5"  required  oninput="this.value = Math.min(5, this.value)">
                @error('Cantidad_menores')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>           

            <div id="edadMenoresContainer" class="form-group" style="display: none;">
                <label for="Edad_menores">Edad de los Menores</label>
                <div id="edadMenoresInputs"></div>

            </div>

            <button type="submit" class="btn btn-primary">Obtener Disponibilidad</button>
        </form>
    </div>    
   <script>
    document.addEventListener('DOMContentLoaded', function() {
        const cantidadMenoresInput = document.getElementById('Cantidad_menores');
        const edadMenoresContainer = document.getElementById('edadMenoresContainer');
        const edadMenoresInputs = document.getElementById('edadMenoresInputs');

        // Actualiza los campos de edad según la cantidad de menores
        cantidadMenoresInput.addEventListener('input', function() {
            const cantidadMenores = parseInt(cantidadMenoresInput.value);

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

                    const input = document.createElement('input');
                    input.type = 'number';
                    input.id = `Edad_menor_${i}`;
                    input.name = `Edad_menores[${i}]`;
                    input.classList.add('form-control');
                    input.placeholder = `Edad del menor ${i}`;

                    // Crea el contenedor para el error
                    const errorContainer = document.createElement('div');
                    errorContainer.id = `error_Edad_menor_${i}`;
                    errorContainer.classList.add('text-danger');
                    
                    // Agrega el label, input y contenedor de error al contenedor
                    edadMenoresInputs.appendChild(label);
                    edadMenoresInputs.appendChild(input);
                    edadMenoresInputs.appendChild(errorContainer);  // Contenedor de error
                }
            } else {
                edadMenoresContainer.style.display = 'none';
            }
        });
    });


    document.addEventListener('DOMContentLoaded', function() {
            const ciudadSelect = document.getElementById('Ciudad_Id_Ciudad');
            const zonaOrigenSelect = document.getElementById('Zona_Origen_id');
            const zonaDestinoSelect = document.getElementById('Zona_Destino_id');
            const tipoServicioSelect = document.getElementById('Tipo_servicio_transfer'); // Campo para seleccionar el tipo de servicio
            
            // Evento para manejar el cambio de tipo de servicio
            tipoServicioSelect.addEventListener('change', function() {
                const ciudadId = this.value;
                
                const tipoServicio = tipoServicioSelect.value;  // Obtener el tipo de servicio seleccionado
               
                // Si no se seleccionó una ciudad, limpiamos las zonas
                if (!ciudadId) {
                    zonaOrigenSelect.innerHTML = '<option value="">Selecciona una zona</option>';
                    zonaDestinoSelect.innerHTML = '<option value="">Selecciona una zona</option>';
                    return;
                }

                // Realizar la petición AJAX para obtener las zonas de la ciudad seleccionada
                fetch(`/traslados/zonas-origen/${ciudadId}/${tipoServicio}`)
                    .then(response => response.json())
                    .then(data => { 
                        // Limpiar las opciones de las zonas
                        zonaOrigenSelect.innerHTML = '<option value="">Selecciona una zona</option>';
                        zonaDestinoSelect.innerHTML = '<option value="">Selecciona una zona</option>';

                        // Añadir las zonas de origen al select
                        data.zonas.forEach(zona => {
                            const option = document.createElement('option');
                            option.value = zona.Id_Zona;
                            option.textContent = `Zona ${zona.nombre_zona}`;
                            zonaOrigenSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error al cargar las zonas:', error);
                    });
            });

            // Evento para manejar el cambio en la ciudad
            ciudadSelect.addEventListener('change', function() {
                const ciudadId = this.value;
                
                const tipoServicio = tipoServicioSelect.value;  // Obtener el tipo de servicio seleccionado
               
                // Si no se seleccionó una ciudad, limpiamos las zonas
                if (!ciudadId) {
                    zonaOrigenSelect.innerHTML = '<option value="">Selecciona una zona</option>';
                    zonaDestinoSelect.innerHTML = '<option value="">Selecciona una zona</option>';
                    return;
                }

                // Realizar la petición AJAX para obtener las zonas de la ciudad seleccionada
                fetch(`/traslados/zonas-origen/${ciudadId}/${tipoServicio}`)
                    .then(response => response.json())
                    .then(data => { 
                        // Limpiar las opciones de las zonas
                        zonaOrigenSelect.innerHTML = '<option value="">Selecciona una zona</option>';
                        zonaDestinoSelect.innerHTML = '<option value="">Selecciona una zona</option>';

                        // Añadir las zonas de origen al select
                        data.zonas.forEach(zona => {
                            const option = document.createElement('option');
                            option.value = zona.Id_Zona;
                            option.textContent = `Zona ${zona.nombre_zona}`;
                            zonaOrigenSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error al cargar las zonas:', error);
                    });
            });

            // Evento para manejar el cambio en la zona de origen
            zonaOrigenSelect.addEventListener('change', function() {
                const zonaId = this.value;
                

                // Si no se seleccionó una zona de origen, limpiamos la zona de destino
                if (!zonaId) {
                    zonaDestinoSelect.innerHTML = '<option value="">Selecciona una zona</option>';
                    return;
                }
                
                // Realizar la petición AJAX para obtener las zonas de destino asociadas a la zona de origen seleccionada
                fetch(`/traslados/zonas-destino/${zonaId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Limpiar las opciones de las zonas de destino
                        zonaDestinoSelect.innerHTML = '<option value="">Selecciona una zona</option>';

                        // Añadir las zonas de destino al select
                        data.zonas.forEach(zona => {
                            const option = document.createElement('option');
                            option.value = zona.Id_Zona;
                            option.textContent = `Zona ${zona.nombre_zona}`;
                            zonaDestinoSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error al cargar las zonas de destino:', error);
                    });
            });
            // Inicializar Bootstrap Datepicker
            $('#Fecha_disponible').datepicker({
                format: 'dd/mm/yyyy', // Formato de fecha
                startDate: 'today',   // No permitir fechas pasadas
                autoclose: true,      // Cerrar automáticamente después de seleccionar
                todayHighlight: true, // Resaltar la fecha actual
                language: 'es',       // Traducir al español
                templates: {
                    leftArrow: '<i class="fas fa-chevron-left text-primary"></i>',
                    rightArrow: '<i class="fas fa-chevron-right text-primary"></i>'
                }
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
<script>
$(document).ready(function () {
    // Inicializa Select2
    $('.select2-ciudades').select2();

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
                            text: `Zona ${zona.nombre_zona}`
                        })
                    );
                });
            })
            .catch(error => {
                console.error('Error al cargar las zonas:', error);
            });
    });

    // También rehaz lo mismo para Tipo de servicio si es necesario
    $('#Tipo_servicio_transfer').on('change', function () {
        $('#Ciudad_Id_Ciudad').trigger('change'); // Forzar recarga de zonas
    });
});
</script>

{{-- Mostrar errores después de que el formulario sea enviado --}}
@foreach(range(1, old('Cantidad_menores', 0)) as $i)
    @error("Edad_menores.$i")
        <span class="text-danger">{{ $message }}</span>
    @enderror
@endforeach
@endsection
