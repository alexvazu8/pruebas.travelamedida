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
        <div id="countdown"></div>

        {{-- Mostrar el precio total del carrito si está disponible --}}
        @if(isset($respuestas['Precio_total_carrito']))
            @php
            $exp=$respuestas[0]['expiration_token'];
            @endphp
            <script>
                // Timestamp de expiración (pasado desde Laravel)
                const expTimestamp = {{ $exp }};

                function updateCountdown() {
                    const now = Math.floor(Date.now() / 1000); // Timestamp actual en segundos
                    const remainingTime = expTimestamp - now;

                    if (remainingTime <= 0) {
                        document.getElementById('countdown').innerHTML = "Token expirado";
                        return;
                    }

                    // Convertir segundos a horas, minutos y segundos
                    const hours = Math.floor(remainingTime / 3600);
                    const minutes = Math.floor((remainingTime % 3600) / 60);
                    const seconds = remainingTime % 60;

                    document.getElementById('countdown').innerHTML = 
                        `Tiempo restante: ${hours}h ${minutes}m ${seconds}s`;

                    // Actualizar cada segundo
                    setTimeout(updateCountdown, 1000);
                }

                // Iniciar el contador
                updateCountdown();
            </script>
            <div class="alert alert-info">
                <strong>Precio Total del Carrito:</strong> {{ number_format($respuestas['Precio_total_carrito'], 2) }}
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
                    <!-- Modal QR-->
                    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="qrModalLabel">Escanea el código QR USDT Crypto para pagar, Red POLYGON</h5>
                                </div>
                                <div class="modal-body text-center">
                                    <img id="qrImagen" src="" alt="Código QR" style="width:300px;">
                                    <p class="mt-3">Esperando pago Crypto USDT... No cierres esta ventana.</p>
                                    <button id="btnDescargarQR" class="btn btn-success">Descargar QR</button>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                    <!-- Fin Modal QR-->
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
                console.log('transactionId',transactionId);

                document.getElementById('qrImagen').src = 'data:image/png;base64,' + qrBase64;

                const qrModal = new bootstrap.Modal(document.getElementById('qrModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
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

    </script>
@endsection
