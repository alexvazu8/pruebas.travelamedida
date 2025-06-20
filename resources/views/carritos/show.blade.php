@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-xl font-bold mb-4">Detalles del Carrito</h1>

    {{-- Mostrar mensaje de éxito o error --}}
    @if(isset($mensaje))
        <div class="alert {{ $mensaje === 'Exito!!!' ? 'alert-success' : 'alert-danger' }}">
            {{ $mensaje }}
        </div>
    @endif

    {{-- Verificar si hay respuestas --}}
    @if(isset($respuestas) && is_array($respuestas))
        <!-- Muesta el contador de en cuanto exprira el carrito-->
        <div class="countdown-container">
            <div class="countdown-text" id="countdown-text"></div>
            <div class="progress-bar">
                <div id="countdown-progress" class="progress"></div>
            </div>
        </div>

        {{-- Mostrar el precio total del carrito si está disponible --}}
        @if(isset($respuestas['Precio_total_carrito']))
            @php
            $exp=$respuestas[0]['expiration_token'];
            @endphp
            <script>
                // Timestamp de expiración (pasado desde Laravel)
                const expTimestamp = {{ $exp }};
                const startTime = Math.floor(Date.now() / 1000); // Timestamp inicial
                const totalDuration = expTimestamp - startTime;

                function updateCountdown() {
                    const now = Math.floor(Date.now() / 1000); // Timestamp actual en segundos
                    const remainingTime = expTimestamp - now;
                    const elapsedTime = now - startTime;
                    const progressPercentage = (elapsedTime / totalDuration) * 100;

                    // Seleccionar elementos
                    const progressBar = document.getElementById('countdown-progress');
                    const countdownText = document.getElementById('countdown-text');

                    if (remainingTime <= 0) {
                        countdownText.innerHTML = "¡Tiempo agotado!";
                        progressBar.style.width = '100%';
                        progressBar.className = 'progress critical';
                        // Recargar la página
                        setTimeout(() => {
                            location.reload();
                        }, 1000); // Espera 1 segundo antes de recargar
                        
                        return;
                    }

                    // Convertir segundos a horas, minutos y segundos
                    const hours = Math.floor(remainingTime / 3600);
                    const minutes = Math.floor((remainingTime % 3600) / 60);
                    const seconds = remainingTime % 60;

                    // Actualizar texto
                    countdownText.innerHTML = `Tiempo restante: ${hours}h ${minutes}m ${seconds}s`;

                    // Actualizar barra de progreso
                    progressBar.style.width = `${100 - progressPercentage}%`;
                    
                    // Cambiar colores según el tiempo restante
                    if (remainingTime < totalDuration * 0.3) { // Menos del 30% del tiempo
                        progressBar.className = 'progress critical';
                    } else if (remainingTime < totalDuration * 0.6) { // Menos del 60% del tiempo
                        progressBar.className = 'progress warning';
                    } else {
                        progressBar.className = 'progress';
                    }

                    // Actualizar cada segundo
                    setTimeout(updateCountdown, 1000);
                }

                // Iniciar el contador
                updateCountdown();
            </script>
            <div class="alert alert-info">
                <strong>Precio Total del Carrito:</strong> {{ number_format($respuestas['Precio_total_carrito'], 2) }} USD
            </div>
            {{-- Botón para eliminar el carrito --}}
            <div class="container">
                <form action="{{ route('carritos.borrar') }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este carrito?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Vaciar Carrito</button>
                </form>
            </div>
            {{-- Formulario para agregar los campos adicionales --}}
            <div class="container">
                <form id="formReserva" action="{{ route('reservas.confirmar') }}" method="POST" class="mb-4">
                    @csrf
                    <input type="hidden" id="montoPago" name="montoPago" value="{{ number_format($respuestas['Precio_total_carrito'], 2, '.', '') }}">

                    <div class="row mb-3">
                        <div class="row md-6">
                            <label for="Nombre_titular_reserva">Nombre del Titular de la Reserva</label>
                            <input type="text" name="Nombre_titular_reserva" value="{{ old('Nombre_titular_reserva') }}" id="Nombre_titular_reserva" class="form-control rounded-pill px-4"  maxlength="30"  pattern="[A-Za-zñÑ\s]+" oninput="this.value = this.value.replace(/[^A-Za-zñÑ\s]/g, '')" required>
                        </div>
                        <div class="row md-6">
                            <label for="Apellido_titular_reserva">Apellido del Titular de la Reserva</label>
                            <input type="text" name="Apellido_titular_reserva" value="{{ old('Apellido_titular_reserva') }}" id="Apellido_titular_reserva" class="form-control rounded-pill px-4"  maxlength="30" pattern="[A-Za-zñÑ\s]+" oninput="this.value = this.value.replace(/[^A-Za-zñÑ\s]/g, '')" required>
                        </div>
                        <div class="row md-6">
                            <label for="Telefono_titular_reserva">Telefono del Titular de la Reserva</label>
                            <input type="text" name="Telefono_titular_reserva"  value="{{ old('Telefono_titular_reserva') }}" id="Telefono_titular_reserva" class="form-control rounded-pill px-4" pattern="^[+\d\s]+$" oninput="this.value = this.value.replace(/[^+\d\s]/g, '')" minlength="8" maxlength="20" required>
                        </div>
                        <div class="row mb-3">
                            <label for="Email_contacto_reserva">Email de Contacto</label>
                            <input type="email" name="Email_contacto_reserva" value="{{ old('Email_contacto_reserva') }}" id="Email_contacto_reserva" class="form-control rounded-pill px-4" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" title="Ejemplo: usuario@dominio.com"  required>
                        </div>
                        <div class="row mb-4">
                            <label for="Comentarios" class="col-md-6">Comentarios (Opcional)</label>
                            <textarea name="Comentarios" id="Comentarios" class="form-control rounded-pill px-4 py-2" rows="3" pattern="[A-Za-zñÑ0-9\s\.,]+" oninput="this.value = this.value.replace(/[^A-Za-zñÑ0-9\s.,]/g, '')">{{ old('Comentarios') }}</textarea>
                        </div>
                        
                         <button type="button" id="btnMostrarQR" class="btn btn-primary px-4">Pagar con USDT y Confirmar Reserva</button>
                        
                    </div>
                </form>
                <!-- Modal QR -->
                <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <!-- Header -->
                            <div class="modal-header">
                                <h5 class="modal-title" id="qrModalLabel">
                                <span class="d-flex align-items-center justify-content-center flex-wrap mt-2">
                                    Escanea el código QR USDT
                                    <img src="{{ asset('images/USDT.png') }}" alt="USDT" style="height: 30px;" class="me-2">
                                    para pagar, Red POLYGON
                                    <img src="{{ asset('images/POLYGON.png') }}" alt="Red Polygon" style="height: 30px;" class="ms-2 me-2">
                                    <small class="text-danger w-100 text-center mt-1">(el uso de otra red causará pérdida de fondos)</small>
                                </span>
                                </h5>
                            </div>

                            <!-- Body -->
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <!-- Primera fila: Monto y QR -->
                                    <div class="row align-items-center">
                                        <div class="col-md-5 text-center mb-3 mb-md-0"> 
                                            <!-- Cuenta regresiva -->
                                            <div class="cuenta-regresiva-electronica mt-3 mx-auto" style="width: 140px; height: 140px; position: relative;">
                                                <svg id="progresoCircular" width="140" height="140">
                                                <circle cx="70" cy="70" r="60" stroke="#2c2c2c" stroke-width="12" fill="none" />
                                                <circle id="progreso" cx="70" cy="70" r="60" stroke="#00ffcc" stroke-width="8" fill="none"
                                                    stroke-linecap="round"
                                                    stroke-dasharray="377" stroke-dashoffset="377"
                                                    transform="rotate(-90 70 70)" />
                                                </svg>
                                                <div id="tiempoRestanteTexto"  class="position-absolute top-50 start-50 translate-middle text-light fw-bold fs-5 px-2 py-1 rounded" style="background-color: rgba(0, 0, 0, 0.6); z-index: 2;">
                                                --:--
                                                </div>
                                            </div>
                                            <!-- Monto -->
                                            <div id="montoSobreQR" class="bg-dark bg-opacity-75 text-white px-3 py-2 rounded" style="font-size: 1.1rem;">
                                                <!-- Se actualizará con JS -->
                                            </div>
                                        </div>
                                        
                                        <!-- QR -->
                                        <div class="col-md-7 text-center">
                                        <img id="qrImagen" src="" alt="Código QR" style="max-width: 280px; width: 100%;" class="img-fluid rounded shadow mb-3">
                                        </div>
                                    </div>
                                
                                    <!-- Segunda fila: Mensaje -->
                                    <div class="row mt-3">
                                        <div class="col-12 text-center">
                                            <!-- Wallet + copiar -->
                                            <div class="input-group" style="max-width: 100%;">
                                                <input type="text" id="walletDestino" class="form-control text-center fw-bold" readonly>
                                                <button class="btn btn-outline-secondary" type="button" id="btnCopiarWallet">Copiar</button>
                                            </div>
                                            <!-- Boton de Descarga -->
                                            <button id="btnDescargarQR" class="btn btn-success mt-3">Descargar QR</button>
                                            <p class="mb-0">Esperando pago Crypto USDT... No cierres esta ventana.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fin Modal QR -->

            </div>
        @endif

        {{-- Iterar sobre los carritos --}}
        @foreach($respuestas as $key => $carrito)
            @if(is_array($carrito)) {{-- Asegurarse de que sea un carrito válido --}}
                <div class="card mb-4">
                    <div class="card-header font-bold">Servicio #{{ $carrito['id'] ?? 'N/A' }}</div>
                    <div class="card-body">
                        {{-- Información del carrito principal --}}
                        <p><strong>Tipo de Servicio:</strong> 
                            @if(isset($carrito['Tipo_servicio']))
                                @if($carrito['Tipo_servicio'] === 'T')
                                    Traslado
                                @elseif($carrito['Tipo_servicio'] === 'TOU')
                                    Tour
                                @elseif($carrito['Tipo_servicio'] === 'H')
                                    Hotel
                                @else
                                    {{ $carrito['Tipo_servicio'] }}
                                @endif
                            @else
                                N/A
                            @endif
                        </p>
                        <p><strong>Precio Total:</strong> {{ isset($carrito['Precio_Total']) ? number_format($carrito['Precio_Total'], 2) : 'N/A' }}</p>
                        <p><strong>Fecha de Creación:</strong> {{ isset($carrito['created_at']) ? \Carbon\Carbon::parse($carrito['created_at'])->translatedFormat('d F Y') : 'N/A' }}</p>
                        <p><strong>Fecha de Actualización:</strong> {{ isset($carrito['updated_at']) ? \Carbon\Carbon::parse($carrito['updated_at'])->translatedFormat('d F Y') : 'N/A' }}</p>
                        {{-- Campo opcional: Email del encargado --}}
                        @if(isset($carrito['Email_encargado_reserva']))
                            <p><strong>Email del Encargado de Reserva:</strong> {{ $carrito['Email_encargado_reserva'] }}</p>
                        @endif
                    </div>

                    {{-- Mostrar detalle del carrito --}}
                    <div class="card mt-3">
                        <div class="card-header font-bold">Detalle del Servicio</div>
                        <div class="card-body">
                            @if(isset($carrito['detalle']) && is_array($carrito['detalle']))
                                @if($carrito['Tipo_servicio'] === 'H')
                                    {{-- Detalles del Hotel --}}
                                    <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Fecha de Entrada</th>
                                                <th>Fecha de Salida</th>
                                                <th>Tipo de Habitación</th>
                                                <th>Régimen</th>
                                                <th>Cant. Adultos</th>
                                                <th>Cant. Menores</th>
                                                <th>Cant. Noches</th>
                                                <th>Precio Promedio por Noche</th>
                                                <th>Precio Total</th>
                                                <th>Cant. Habitaciones</th>
                                                <th>Penalidad</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($carrito['detalle'] as $detalle)
                                                <tr>
                                                    <td>{{ isset($detalle['Fecha_In']) ? \Carbon\Carbon::parse($detalle['Fecha_In'])->translatedFormat('d F Y') : 'N/A' }}</td>
                                                    <td>{{ isset($detalle['Fecha_Out']) ? \Carbon\Carbon::parse($detalle['Fecha_Out'])->translatedFormat('d F Y') : 'N/A' }}</td>
                                                    <td>{{ $detalle['Nombre_Habitacion'] ?? 'N/A' }}</td>
                                                    <td>{{ $detalle['Nombre_Regimen'] ?? 'N/A' }}</td>
                                                    <td>{{ $detalle['Cantidad_Adultos'] ?? 'N/A' }}</td>
                                                    <td>{{ $detalle['Cantidad_Menores'] ?? 'N/A' }}</td>
                                                    <td>{{ $detalle['Cantidad_Noches'] ?? 'N/A' }}</td>
                                                    <td>{{ isset($detalle['Precio_promedio_por_noche']) ? number_format($detalle['Precio_promedio_por_noche'], 2) : 'N/A' }}</td>
                                                    <td>{{ isset($detalle['Precio_total_habitacion']) ? number_format($detalle['Precio_total_habitacion'], 2) : 'N/A' }}</td>
                                                    <td>{{ $detalle['Cantidad_habitaciones'] ?? 'N/A' }}</td>
                                                    <td>
                                                        @if(isset($detalle['politica']['penalidads']) && count($detalle['politica']['penalidads']) > 0)
                                                            @php
                                                                $textoPenalidad = '';
                                                                foreach ($detalle['politica']['penalidads'] as $penalidad) {
                                                                    $textoPenalidad .= "Desde {$penalidad['desde_noches_antes']} hasta {$penalidad['hasta_noches_antes']} noches antes: {$penalidad['porcentaje_penalidad_por_noche']} de la reserva.<br>";
                                                                }
                                                            @endphp
                                                            <button 
                                                                class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#penalidadModal" 
                                                                onclick="document.getElementById('penalidadContent').innerHTML = `{!! addslashes($textoPenalidad) !!}`;">
                                                                ℹ️
                                                            </button>
                                                        @else
                                                            No aplica
                                                        @endif
                                                    </td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                @elseif($carrito['Tipo_servicio'] === 'T')
                                    {{-- Detalles del Traslado --}}
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Fecha del Servicio</th>
                                                <th>Hora del Servicio</th>
                                                <th>Cant. Adultos</th>
                                                <th>Cant. Menores</th>
                                                <th>Precio Adulto</th>
                                                <th>Precio Menor</th>
                                                <th>Precio Total</th>
                                                <th>Marca/Modelo</th>
                                                <th>Maletas Máximo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($carrito['detalle'] as $detalle)
                                                <tr>
                                                    <td>{{ isset($detalle['fecha_servicio']) ? \Carbon\Carbon::parse($detalle['fecha_servicio'])->translatedFormat('d F Y') : 'N/A' }}</td>
                                                    <td>{{ $detalle['hora_servicio'] ?? 'N/A' }}</td>
                                                    <td>{{ $detalle['Cantidad_Adultos'] ?? 'N/A' }}</td>
                                                    <td>{{ $detalle['Cantidad_Menores'] ?? 'N/A' }}</td>
                                                    <td>{{ isset($detalle['Precio_Adulto']) ? number_format($detalle['Precio_Adulto'], 2) : 'N/A' }}</td>
                                                    <td>{{ isset($detalle['Precio_Menor']) ? number_format($detalle['Precio_Menor'], 2) : 'N/A' }}</td>
                                                    <td>{{ isset($detalle['Precio_Total']) ? number_format($detalle['Precio_Total'], 2) : 'N/A' }}</td>
                                                    <td>{{ isset($detalle['empresa_traslado_tipo_movilidade']['Marca_modelo']) ? $detalle['empresa_traslado_tipo_movilidade']['Marca_modelo'] : 'N/A' }}</td>
                                                    <td>{{ isset($detalle['empresa_traslado_tipo_movilidade']['Maletas_maximo']) ? $detalle['empresa_traslado_tipo_movilidade']['Maletas_maximo'] : 'N/A' }}</td>
                                                </tr>
                                                {{-- Mostrar información del servicio traslado si existe --}}
                                                @if(isset($detalle['servicio_traslado']))
                                                    <tr>
                                                        <td colspan="9">
                                                            <strong>Servicio:</strong> {{ $detalle['servicio_traslado']['Nombre_Servicio'] ?? 'N/A' }} <br>
                                                            <strong>Detalle:</strong> {{ $detalle['servicio_traslado']['Detalle_servicio'] ?? 'N/A' }} <br>
                                                            <strong>Tipo:</strong> {{ $detalle['servicio_traslado']['Tipo_servicio_transfer'] ?? 'N/A' }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                @elseif($carrito['Tipo_servicio'] === 'TOU')
                                    {{-- Detalles del Tour --}}
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nombre del Tour</th>
                                                <th>Fecha de Inicio</th>
                                                <th>Fecha de Fin</th>
                                                <th>Cant. Adultos</th>
                                                <th>Cant. Menores</th>
                                                <th>Precio Adulto</th>
                                                <th>Precio Menor</th>
                                                <th>Precio Total</th>
                                                <th>Duración</th>
                                                <th>Recojo en Hotel</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($carrito['detalle'] as $detalle)
                                                <tr>
                                                    <td>{{ $detalle['tour']['Nombre_tour'] ?? 'N/A' }}</td>
                                                    <td>{{ isset($detalle['Fecha_In']) ? \Carbon\Carbon::parse($detalle['Fecha_In'])->translatedFormat('d F Y') : 'N/A' }}</td>
                                                    <td>{{ isset($detalle['Fecha_Out']) ? \Carbon\Carbon::parse($detalle['Fecha_Out'])->translatedFormat('d F Y') : 'N/A' }}</td>
                                                    <td>{{ $detalle['Cantidad_Adultos'] ?? 'N/A' }}</td>
                                                    <td>{{ $detalle['Cantidad_Menores'] ?? 'N/A' }}</td>
                                                    <td>{{ isset($detalle['Precio_Adulto']) ? number_format($detalle['Precio_Adulto'], 2) : 'N/A' }}</td>
                                                    <td>{{ isset($detalle['Precio_Menor']) ? number_format($detalle['Precio_Menor'], 2) : 'N/A' }}</td>
                                                    <td>{{ isset($detalle['Precio_Total']) ? number_format($detalle['Precio_Total'], 2) : 'N/A' }}</td>
                                                    <td>{{ $detalle['tour']['cantidad_dias_tour'] ?? 'N/A' }} días / {{ $detalle['tour']['cantidad_noches_tour'] ?? 'N/A' }} noches</td>
                                                    <td>{{ $detalle['tour']['Recojo_hotel'] == 1 ? 'Sí' : 'No' }}</td>
                                                </tr>
                                                {{-- Mostrar información adicional del tour si existe --}}
                                                @if(isset($detalle['tour']))
                                                    <tr>
                                                        <td colspan="10">
                                                            <strong>País:</strong> {{ $detalle['tour']['pais']['Nombre_Pais'] ?? 'N/A' }} <br>
                                                            <strong>Ciudad:</strong> {{ $detalle['tour']['ciudad']['Nombre_Ciudad'] ?? 'N/A' }} <br>
                                                            <strong>Zona:</strong> {{ $detalle['tour']['zona']['Nombre_Zona'] ?? 'N/A' }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            @else
                                <p>No hay detalles disponibles para este carrito.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @else
        <p>No se encontraron datos para mostrar.</p>
    @endif
</div>

<!-- Modal -->
<div class="modal fade" id="penalidadModal" tabindex="-1" aria-labelledby="penalidadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="penalidadModalLabel">Política de Cancelación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" id="penalidadContent">
        <!-- Aquí se cargará el contenido -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
    <script>
        
    document.addEventListener('DOMContentLoaded', function() {
        //vamos a hacer el codigo para copiar la direccion de la wallet
        const btnCopiar = document.getElementById('btnCopiarWallet');
            
            btnCopiar.addEventListener('click', () => {
                copiarAlPortapapeles('walletDestino', btnCopiar);
            });

        //cargar datos al formulario
         cargarDatosFormulario();

        const form = document.getElementById('formReserva');
        qrBase64 = ''; // Variable para almacenar el QR
        // Agrega verificacion y QR
        document.getElementById('btnMostrarQR').addEventListener('click', function() {
        // ✅ Validación nativa HTML5
            if (!form.checkValidity()) {
                form.reportValidity(); // muestra mensajes
                return;
            }

            // Validación OK, generar el QR y mostrar modal
            generarQR();
        });
        // Función para descargar el QR
        document.getElementById('btnDescargarQR').addEventListener('click', function() {
            if (!qrBase64) {
                alert('No hay código QR para descargar');
                return;
            }
            
            // Crear un enlace temporal
            const link = document.createElement('a');
            link.href = 'data:image/png;base64,' + qrBase64;
            link.download = 'pago-usdt-qr.png'; // Nombre del archivo
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

        function generarQR() {
            const monto = document.getElementById('montoPago').value;
            
            fetch("{{ route('pagos.charge') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    amount: monto  // aquí pones el monto que quieras enviar
                })
            })
            .then(response => response.json())
            .then(data => {
                 console.log('datos: ',data);  // <--- Mira qué datos llegan aquí
                if (!data.data) {
                    
                    alert('No se pudo obtener el código QR');
                    return;
                }
                qrBase64 = data.data.qr_base64;
                const transactionId = data.data.id;
                const billetera = data.data.collecting_account;
                const tiempoExpira=data.data.expiration_time;
                console.log('transactionId',transactionId);

                document.getElementById('qrImagen').src = 'data:image/png;base64,' + qrBase64;
                document.getElementById('montoSobreQR').innerText = `USDT ${monto}`;
                document.getElementById('walletDestino').value = `${billetera}`;
                guardarDatosFormulario(); // Guarda info del formulario
                const qrModal = new bootstrap.Modal(document.getElementById('qrModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
                const circle = document.getElementById('progreso');
                const radius = circle.r.baseVal.value;
                const circumference = 2 * Math.PI * radius;
                circle.style.strokeDasharray = `${circumference}`;
                circle.style.strokeDashoffset = `${circumference}`;

                const ahoraInicial = Date.now();
                const duracionTotal = tiempoExpira - ahoraInicial;

                function actualizarCuentaRegresiva() {
                    const ahora = Date.now();
                    const tiempoRestante = tiempoExpira - ahora;
                    const segundosRestantes = Math.max(0, Math.floor(tiempoRestante / 1000));

                    const minutos = Math.floor(segundosRestantes / 60).toString().padStart(2, '0');
                    const segundos = (segundosRestantes % 60).toString().padStart(2, '0');
                    document.getElementById('tiempoRestanteTexto').textContent = `${minutos}:${segundos}`;

                    const progreso = Math.min(1, 1 - tiempoRestante / duracionTotal);
                    const offset = circumference * (1 - progreso);
                    circle.style.strokeDashoffset = offset;

                    // Cambio de color dinámico (verde → amarillo → rojo)
                    if (progreso > 0.75) {
                        circle.style.stroke = "#ff4d4d"; // rojo
                    } else if (progreso > 0.5) {
                        circle.style.stroke = "#ffc107"; // amarillo
                    } else {
                        circle.style.stroke = "#00ffcc"; // verde
                    }

                    if (segundosRestantes <= 0) {
                        clearInterval(intervaloTemporizador);
                        document.getElementById('tiempoRestanteTexto').textContent = "⛔ Expirado";
                        setTimeout(() => location.reload(), 1000);
                    }
                }

                const intervaloTemporizador = setInterval(actualizarCuentaRegresiva, 1000);
                actualizarCuentaRegresiva();

                qrModal.show();

                const checkInterval = setInterval(() => {
                    fetch("{{ url('pagos/status') }}/" + transactionId, {
                        method: 'POST', // <-- especificar método POST
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(res => res.json())
                    .then(statusData => {
                        console.log(statusData)
                        if (statusData.estado.toLowerCase() === 'pagado') {
                            clearInterval(checkInterval);
                            qrModal.hide();
                            document.getElementById('formReserva').submit();
                        }
                    })
                    .catch(err => console.error('Error consultando estado pago:', err));
                }, 2000);
            })
            .catch(err => {
                console.error('Error al crear cargo:', err);
                alert('Error al generar el pago.');
            });
        }
    });

    //funcion para guardar datos del formulario.
    const guardarDatosFormulario = () => {
        const nombre = document.getElementById('Nombre_titular_reserva').value;
        const apellido = document.getElementById('Apellido_titular_reserva').value;
        const telefono = document.getElementById('Telefono_titular_reserva').value;
        const email = document.getElementById('Email_contacto_reserva').value;
        const comentarios = document.getElementById('Comentarios').value;
        localStorage.setItem('Nombre_titular_reserva', nombre);
        localStorage.setItem('Apellido_titular_reserva', apellido);
        localStorage.setItem('Telefono_titular_reserva', telefono);
        localStorage.setItem('Email_contacto_reserva', email);
        localStorage.setItem('Comentarios', comentarios);
    };
    function copiarAlPortapapeles(inputId, buttonElement) {
        const input = document.getElementById(inputId);
        const texto = input.value;
       

        if (navigator.clipboard) {
            navigator.clipboard.writeText(texto).then(() => {
                buttonElement.textContent = 'Copiado ✅';
                setTimeout(() => {
                    buttonElement.textContent = 'Copiar';
                }, 2000);
            }).catch(err => {
                alert('Error al copiar: ' + err);
            });
        } else {
            // Fallback para navegadores antiguos
            input.select();
            document.execCommand('copy');
            buttonElement.textContent = 'Copiado ✅';
            setTimeout(() => {
                buttonElement.textContent = 'Copiar';
            }, 2000);
        }
    }

    function cargarDatosFormulario() {
        const campos = [
            'Nombre_titular_reserva',
            'Apellido_titular_reserva',
            'Telefono_titular_reserva',
            'Email_contacto_reserva',
            'Comentarios'
        ];

        campos.forEach(campo => {
            const valorGuardado = localStorage.getItem(campo);
            if (valorGuardado) {
                const input = document.getElementById(campo);
                if (input) {
                    input.value = valorGuardado;
                }
                localStorage.removeItem(campo); // Opcional: limpia después de usar
            }
        });
    }



    </script>
@endsection
