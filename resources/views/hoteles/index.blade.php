@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('Titulo_disponibilidad_hoteles') }}</h1>
    
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('hoteles.obtener') }}" method="POST" id="hoteles-form">
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
                <input type="date" class="form-control" id="Fecha_desde" name="Fecha_desde" 
                       min="{{ now()->format('Y-m-d') }}" 
                       max="{{ now()->addDays(365)->format('Y-m-d') }}" 
                       value="{{ old('Fecha_desde') }}"
                       onfocus="this.showPicker && this.showPicker()" 
                       onclick="this.showPicker && this.showPicker()" 
                       required 
                       onchange="const nextDay = new Date(this.value); nextDay.setDate(nextDay.getDate() + 1); document.getElementById('Fecha_hasta').min = nextDay.toISOString().split('T')[0];">
            </div>

            <!-- Fecha Hasta -->
            <div class="form-group col-md-2">
                <label for="Fecha_hasta">{{ __('Fecha_hasta') }}</label>
                <input type="date" class="form-control" id="Fecha_hasta" name="Fecha_hasta" 
                       min="{{ now()->addDay()->format('Y-m-d') }}" 
                       max="{{ now()->addDays(365)->format('Y-m-d') }}"
                       value="{{ old('Fecha_hasta') }}"
                       onfocus="this.showPicker && this.showPicker()" 
                       onclick="this.showPicker && this.showPicker()" 
                       required>
            </div>

            <!-- Número de Habitaciones -->
            <div class="form-group col-md-2 position-relative">
                <label for="Numero_Habitaciones">{{ __('Numero_habitaciones') }}</label>
                <input type="number" class="form-control" id="Numero_Habitaciones" name="Numero_Habitaciones" 
                       min="1" max="3" value="{{ old('Numero_Habitaciones', 1) }}" required>
                
                <!-- Contenedor dinámico de habitaciones -->
                <div id="habitaciones-container" class="position-absolute mt-1" style="display: none; z-index: 1000;">
                    <div class="habitaciones-header d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 text-dark">Configuración de Habitaciones</h6>
                        <button type="button" id="cerrar-habitaciones" class="btn btn-sm btn-secondary">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div id="habitaciones-content">
                        <!-- Se generan dinámicamente habitaciones aquí -->
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
/* Tus estilos CSS existentes se mantienen igual */
#habitaciones-container {
    background: #ffffff;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    padding: 12px;
    width: 320px;
    max-height: 400px;
    overflow-y: auto;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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

.habitacion-compacta:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

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
    const form = document.getElementById('hoteles-form');

    // Inicializar con datos antiguos si existen (después de validación fallida)
    function inicializarConDatosAntiguos() {
        const numeroHabitaciones = parseInt(habitacionesInput.value) || 1;
        
        // Limpiar contenido existente
        habitacionesContent.innerHTML = '';
        
        // Crear habitaciones según el número
        for (let i = 1; i <= numeroHabitaciones; i++) {
            crearHabitacion(i);
        }
        
        // Mostrar el contenedor
        habitacionesContainer.style.display = 'block';
    }

    function crearHabitacion(habitacionId) {
        // Obtener valores antiguos si existen
        const adultosOld = "{{ old('habitaciones.' . $habitacionId . '.Cantidad_adultos', 1) }}";
        const menoresOld = "{{ old('habitaciones.' . $habitacionId . '.Cantidad_menores', 0) }}";

        const habitacionDiv = document.createElement('div');
        habitacionDiv.classList.add('habitacion-compacta');
        habitacionDiv.id = `habitacion_${habitacionId}`;
        habitacionDiv.innerHTML = `
            <div class="habitacion-header">{{ __('Habitacion') }} ${habitacionId}</div>
            <div class="row habitacion-campos">
                <!-- Cantidad de Adultos -->
                <div class="form-group col-6">
                    <label for="Cantidad_adultos_${habitacionId}">{{ __('Adultos') }}</label>
                    <input type="number" class="form-control" 
                           id="Cantidad_adultos_${habitacionId}" 
                           name="habitaciones[${habitacionId}][Cantidad_adultos]" 
                           min="1" max="4" value="${adultosOld}" required>
                </div>

                <!-- Cantidad de Menores -->
                <div class="form-group col-6">
                    <label for="Cantidad_menores_${habitacionId}">{{ __('Menores') }}</label>
                    <input type="number" class="form-control cantidad-menores" 
                           id="Cantidad_menores_${habitacionId}" 
                           name="habitaciones[${habitacionId}][Cantidad_menores]" 
                           min="0" max="4" value="${menoresOld}" 
                           data-habitacion-id="${habitacionId}" required>
                </div>
            </div>

            <!-- Edad de los Menores -->
            <div class="edades-menores-container" id="edades_menores_${habitacionId}">
                <!-- Campos dinámicos de edades -->
            </div>
        `;
        habitacionesContent.appendChild(habitacionDiv);
        
        // Inicializar edades para esta habitación
        actualizarEdadesMenores(habitacionId);
    }

    function actualizarHabitaciones() {
        const numeroHabitaciones = parseInt(habitacionesInput.value) || 1;
        
        // Limitar entre 1 y 3
        if (numeroHabitaciones < 1) {
            habitacionesInput.value = 1;
        } else if (numeroHabitaciones > 3) {
            habitacionesInput.value = 3;
        }

        const habitacionesExistentes = habitacionesContent.querySelectorAll('.habitacion-compacta');
        const numHabitacionesExistentes = habitacionesExistentes.length;
        const numeroFinal = parseInt(habitacionesInput.value);
        
        if (numeroFinal > numHabitacionesExistentes) {
            for (let i = numHabitacionesExistentes + 1; i <= numeroFinal; i++) {
                crearHabitacion(i);
            }
        } else if (numeroFinal < numHabitacionesExistentes) {
            for (let i = numHabitacionesExistentes; i > numeroFinal; i--) {
                const habitacion = document.getElementById(`habitacion_${i}`);
                if (habitacion) {
                    habitacion.remove();
                }
            }
        }
        
        habitacionesContainer.style.display = 'block';
    }

    function actualizarEdadesMenores(habitacionId) {
        const cantidadMenoresInput = document.getElementById(`Cantidad_menores_${habitacionId}`);
        const edadesMenoresContainer = document.getElementById(`edades_menores_${habitacionId}`);
        
        let cantidadMenores = parseInt(cantidadMenoresInput.value) || 0;

        // Validar y ajustar
        if (cantidadMenores < 0) cantidadMenores = 0;
        if (cantidadMenores > 4) {
            cantidadMenores = 4;
            cantidadMenoresInput.value = 4;
        }

        edadesMenoresContainer.innerHTML = '';
        
        if (cantidadMenores > 0) {
            const edadesRow = document.createElement('div');
            edadesRow.classList.add('row', 'edades-compactas', 'mt-1');
            edadesRow.innerHTML = '<div class="col-12"><label class="mb-1" style="font-size: 0.7rem; font-weight: 500;">{{ __('Edades_menores') }}:</label></div>';
            
            for (let i = 1; i <= cantidadMenores; i++) {
                // Obtener valor antiguo si existe
                const edadOld = "{{ old('habitaciones.' . $habitacionId . '.Edad_menores.' . $i, '') }}";
                
                const edadCol = document.createElement('div');
                edadCol.classList.add('form-group', 'col-6');
                edadCol.innerHTML = `
                    <label for="Edad_menor_${habitacionId}_${i}" style="font-size: 0.65rem;">Menor ${i}</label>
                    <input type="number" class="form-control" 
                           id="Edad_menor_${habitacionId}_${i}" 
                           name="habitaciones[${habitacionId}][Edad_menores][${i}]" 
                           min="0" max="17" value="${edadOld}" required>
                `;
                edadesRow.appendChild(edadCol);
            }
            edadesMenoresContainer.appendChild(edadesRow);
        }
    }

    function ocultarHabitaciones() {
        habitacionesContainer.style.display = 'none';
    }

    function mostrarHabitaciones() {
        // Si no hay habitaciones, crear una
        if (habitacionesContent.children.length === 0) {
            crearHabitacion(1);
        }
        habitacionesContainer.style.display = 'block';
    }

    // Validación personalizada antes del envío
    form.addEventListener('submit', function(e) {
        let formularioValido = true;
        const errores = [];

        // Validar que todas las habitaciones tengan datos completos
        const habitaciones = habitacionesContent.querySelectorAll('.habitacion-compacta');
        
        habitaciones.forEach((habitacion, index) => {
            const habitacionId = index + 1;
            const adultos = habitacion.querySelector(`[name="habitaciones[${habitacionId}][Cantidad_adultos]"]`);
            const menores = habitacion.querySelector(`[name="habitaciones[${habitacionId}][Cantidad_menores]"]`);
            
            // Validar adultos
            if (!adultos.value || parseInt(adultos.value) < 1) {
                formularioValido = false;
                errores.push(`Habitación ${habitacionId}: La cantidad de adultos es requerida (mínimo 1)`);
                adultos.style.borderColor = 'red';
            } else {
                adultos.style.borderColor = '';
            }

            // Validar menores y sus edades
            if (menores.value && parseInt(menores.value) > 0) {
                const cantidadMenores = parseInt(menores.value);
                for (let i = 1; i <= cantidadMenores; i++) {
                    const edadInput = document.getElementById(`Edad_menor_${habitacionId}_${i}`);
                    if (!edadInput || !edadInput.value || edadInput.value === '') {
                        formularioValido = false;
                        errores.push(`Habitación ${habitacionId}: La edad del menor ${i} es requerida`);
                        if (edadInput) edadInput.style.borderColor = 'red';
                    } else if (edadInput) {
                        edadInput.style.borderColor = '';
                    }
                }
            }
        });

        if (!formularioValido) {
            e.preventDefault();
            alert('Por favor complete todos los campos requeridos:\n' + errores.join('\n'));
        }
    });

    // Inicializar
    inicializarConDatosAntiguos();

    // Event Listeners
    habitacionesInput.addEventListener('focus', mostrarHabitaciones);
    habitacionesInput.addEventListener('input', actualizarHabitaciones);
    
    document.querySelector('label[for="Numero_Habitaciones"]').addEventListener('click', function() {
        habitacionesInput.focus();
        mostrarHabitaciones();
    });

    cerrarBtn.addEventListener('click', ocultarHabitaciones);

    document.addEventListener('click', function(e) {
        if (!habitacionesContainer.contains(e.target) && 
            e.target !== habitacionesInput && 
            e.target !== document.querySelector('label[for="Numero_Habitaciones"]')) {
            ocultarHabitaciones();
        }
    });

    // Delegación de eventos para los inputs de menores
    habitacionesContent.addEventListener('input', function (e) {
        if (e.target.classList.contains('cantidad-menores')) {
            const habitacionId = e.target.getAttribute('data-habitacion-id');
            actualizarEdadesMenores(habitacionId);
        }
    });
});
</script>

<!-- jQuery y Select2 -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.select2-ciudades').select2({
            placeholder: "{{ __('Select_ciudad') }}",
            allowClear: true,
            width: '100%'
        });
    });
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection