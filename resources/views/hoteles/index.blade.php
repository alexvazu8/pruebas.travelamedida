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
            <div class="form-group col-md-2 position-relative">
                <label for="Numero_Habitaciones">{{ __('Numero_habitaciones') }}</label>
                <input type="number" class="form-control" id="Numero_Habitaciones" name="Numero_Habitaciones" min="1" max="3" value="1" required>
                
                <!-- Contenedor dinámico de habitaciones (VISIBLE inicialmente) -->
                <div id="habitaciones-container" class="position-absolute mt-1" style="z-index:1000; background:#fff; border-radius:0.75rem; padding:1rem; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                    <!-- Header con botón cerrar -->
                    <div class="habitaciones-header d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 text-dark">Configuración de Habitaciones</h6>
                        <button type="button" id="cerrar-habitaciones" class="btn btn-sm btn-secondary">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div id="habitaciones-content">
                        <!-- Habitación 1 siempre visible -->
                        <div class="habitacion-compacta" id="habitacion_1">
                            <div class="habitacion-header">Habitación 1</div>
                            <div class="row habitacion-campos">
                                <!-- Cantidad de Adultos -->
                                <div class="form-group col-6">
                                    <label for="Cantidad_adultos_1">Adultos</label>
                                    <input type="number" class="form-control" id="Cantidad_adultos_1" name="habitaciones[1][Cantidad_adultos]" min="1" max="4" value="1" required>
                                </div>

                                <!-- Cantidad de Menores -->
                                <div class="form-group col-6">
                                    <label for="Cantidad_menores_1">Menores</label>
                                    <input type="number" class="form-control cantidad-menores" id="Cantidad_menores_1" name="habitaciones[1][Cantidad_menores]" min="0" max="4" value="0" required>
                                </div>
                            </div>

                            <!-- Edad de los Menores -->
                            <div class="edades-menores-container" id="edades_menores_1">
                                <!-- Campos dinámicos de edades -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón de Buscar -->
            <div class="form-group col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">{{ __('Search') }}</button>
            </div>
        </div>
    </form>
</div>

<style>
#habitaciones-container {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important;
    border: 2px solid #dee2e6;
    border-radius: 10px;
    padding: 15px;
    width: 320px;
    max-height: 400px;
    overflow-y: auto;
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    top: 100%;
    left: 0;
    margin-top: 8px;
     /* ESTO ES LO MÁS IMPORTANTE */
    z-index: 9999 !important;
    position: fixed !important;
    /* Asegurar que esté por encima de TODO */
    background-color: #ffffff !important;
}

.habitaciones-header {
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 8px;
}

.habitaciones-header h6 {
    font-size: 0.9rem;
    font-weight: 600;
}

#cerrar-habitaciones {
    font-size: 0.7rem;
    padding: 2px 6px;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.habitacion-compacta {
    background: #f8f9fa;
    border: 1px solid #ced4da;
    border-radius: 6px;
    padding: 10px;
    margin-bottom: 8px;
}

.habitacion-header {
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 6px;
    color: #495057;
}

.habitacion-campos .form-group {
    margin-bottom: 6px;
}

.habitacion-campos label {
    font-size: 0.75rem;
    margin-bottom: 2px;
    font-weight: 500;
    color: #495057;
}

.habitacion-campos .form-control {
    font-size: 0.8rem;
    padding: 4px 6px;
    height: 30px;
    background: #ffffff;
    border: 1px solid #ced4da;
}

.edades-compactas .form-group {
    margin-bottom: 4px;
}

.edades-compactas label {
    font-size: 0.7rem;
    font-weight: 500;
    color: #495057;
}

.edades-compactas .form-control {
    font-size: 0.75rem;
    padding: 2px 4px;
    height: 26px;
    background: #ffffff;
    border: 1px solid #ced4da;
}

/* Mejorar el scroll */
#habitaciones-container::-webkit-scrollbar {
    width: 6px;
}

#habitaciones-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#habitaciones-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#habitaciones-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Efecto hover para las habitaciones */
.habitacion-compacta:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

/* Estilo para los inputs cuando están en foco */
.habitacion-campos .form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const habitacionesContainer = document.getElementById('habitaciones-container');
    const habitacionesContent = document.getElementById('habitaciones-content');
    const habitacionesInput = document.getElementById('Numero_Habitaciones');
    const cerrarBtn = document.getElementById('cerrar-habitaciones');

    // Función para configurar inputs numéricos que pongan 0 cuando están vacíos
    function configurarInputsNumericos() {
        const inputsNumericos = document.querySelectorAll(`
            #Numero_Habitaciones,
            [name*="Cantidad_adultos"],
            [name*="Cantidad_menores"],
            [name*="Edad_menores"]
        `);

        inputsNumericos.forEach(input => {
            // Verificar si ya está vacío y poner 0
            if (input.value === '') {
                input.value = '0';
            }

            // Evento para cuando el input pierde el foco
            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.value = '0';
                }
            });

            // Evento para cuando cambia el valor (por si acaso)
            input.addEventListener('input', function() {
                if (this.value === '') {
                    this.value = '0';
                }
            });
        });
    }

    // La primera habitación ya está en el HTML, así que no necesitamos crearla

    function crearHabitacion(habitacionId) {
        // Verificar si la habitación ya existe
        if (document.getElementById(`habitacion_${habitacionId}`)) {
            return;
        }

        const habitacionDiv = document.createElement('div');
        habitacionDiv.classList.add('habitacion-compacta');
        habitacionDiv.id = `habitacion_${habitacionId}`;
        habitacionDiv.innerHTML = `
            <div class="habitacion-header">Habitación ${habitacionId}</div>
            <div class="row habitacion-campos">
                <!-- Cantidad de Adultos -->
                <div class="form-group col-6">
                    <label for="Cantidad_adultos_${habitacionId}">Adultos</label>
                    <input type="number" class="form-control" id="Cantidad_adultos_${habitacionId}" name="habitaciones[${habitacionId}][Cantidad_adultos]" min="1" max="4" value="1" required>
                </div>

                <!-- Cantidad de Menores -->
                <div class="form-group col-6">
                    <label for="Cantidad_menores_${habitacionId}">Menores</label>
                    <input type="number" class="form-control cantidad-menores" id="Cantidad_menores_${habitacionId}" name="habitaciones[${habitacionId}][Cantidad_menores]" min="0" max="4" value="0" required>
                </div>
            </div>

            <!-- Edad de los Menores -->
            <div class="edades-menores-container" id="edades_menores_${habitacionId}">
                <!-- Campos dinámicos de edades -->
            </div>
        `;
        habitacionesContent.appendChild(habitacionDiv);
        
        // Configurar los inputs numéricos de esta nueva habitación
        setTimeout(configurarInputsNumericos, 10);
    }

    function actualizarHabitaciones() {
        const numeroHabitaciones = parseInt(habitacionesInput.value) || 1;
        
        // Obtener habitaciones existentes
        const habitacionesExistentes = habitacionesContent.querySelectorAll('.habitacion-compacta');
        const numHabitacionesExistentes = habitacionesExistentes.length;
        
        if (numeroHabitaciones > numHabitacionesExistentes) {
            // Agregar habitaciones faltantes
            for (let i = numHabitacionesExistentes + 1; i <= numeroHabitaciones; i++) {
                crearHabitacion(i);
            }
        } else if (numeroHabitaciones < numHabitacionesExistentes) {
            // Eliminar habitaciones sobrantes (pero mantener al menos 1)
            for (let i = numHabitacionesExistentes; i > Math.max(numeroHabitaciones, 1); i--) {
                const habitacion = document.getElementById(`habitacion_${i}`);
                if (habitacion) {
                    habitacion.remove();
                }
            }
        }
        
        // Configurar inputs numéricos después de actualizar
        setTimeout(configurarInputsNumericos, 10);
    }

    function ocultarHabitaciones() {
        // Ocultar el contenedor
        habitacionesContainer.style.display = 'none';
    }

    function mostrarHabitaciones() {
        // Mostrar el contenedor
        habitacionesContainer.style.display = 'block';
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
            edadesRow.classList.add('row', 'edades-compactas', 'mt-1');
            edadesRow.innerHTML = '<div class="col-12"><label class="mb-1" style="font-size: 0.7rem; font-weight: 500;">Edades menores:</label></div>';
            
            for (let i = 1; i <= cantidadMenores; i++) {
                const edadCol = document.createElement('div');
                edadCol.classList.add('form-group', 'col-6');
                edadCol.innerHTML = `
                    <label for="Edad_menor_${habitacionId}_${i}" style="font-size: 0.65rem;">Menor ${i}</label>
                    <input type="number" class="form-control" id="Edad_menor_${habitacionId}_${i}" name="habitaciones[${habitacionId}][Edad_menores][${i}]" min="0" max="17" value="0" required>
                `;
                edadesRow.appendChild(edadCol);
            }
            edadesMenoresContainer.appendChild(edadesRow);
        }
        
        // Configurar los nuevos inputs de edades
        setTimeout(configurarInputsNumericos, 10);
    }

    // Configurar eventos para la primera habitación existente
    const cantidadMenoresInput1 = document.getElementById('Cantidad_menores_1');
    if (cantidadMenoresInput1) {
        cantidadMenoresInput1.addEventListener('input', function() {
            actualizarEdadesMenores(1);
        });
    }

    // Configurar todos los inputs numéricos inicialmente
    configurarInputsNumericos();

    // Actualizar habitaciones cuando cambia el valor
    habitacionesInput.addEventListener('input', actualizarHabitaciones);

    // Ocultar al hacer clic en el botón cerrar
    cerrarBtn.addEventListener('click', ocultarHabitaciones);

    // Mostrar al hacer clic en el campo de número de habitaciones
    habitacionesInput.addEventListener('focus', mostrarHabitaciones);

    // También mostrar al hacer clic en el label
    document.querySelector('label[for="Numero_Habitaciones"]').addEventListener('click', function() {
        habitacionesInput.focus();
        mostrarHabitaciones();
    });

    // Ocultar al hacer clic fuera del contenedor
    document.addEventListener('click', function(e) {
        if (!habitacionesContainer.contains(e.target) && 
            e.target !== habitacionesInput && 
            e.target !== document.querySelector('label[for="Numero_Habitaciones"]')) {
            ocultarHabitaciones();
        }
    });

    // Delegación de eventos para los campos de menores dinámicos
    habitacionesContent.addEventListener('input', function (e) {
        if (e.target.classList.contains('cantidad-menores')) {
            const habitacionId = e.target.id.split('_')[2];
            actualizarEdadesMenores(habitacionId);
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

<!-- Font Awesome para el icono (si no lo tienes incluido) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection