@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-xl font-bold mb-4">{{__("Carrito")}}</h1>

    {{-- Mostrar mensaje de éxito o error --}}
    @if(isset($mensaje))
        <div class="alert {{ $mensaje === 'Exito!!!' ? 'alert-success' : 'alert-danger' }}">
            {{ $mensaje }}
        </div>
    @endif

    {{-- Verificar si hay respuestas --}}
    @if(isset($respuestas) && is_array($respuestas))

        {{-- Iterar sobre los carritos --}}
        @foreach($respuestas as $key => $carrito)
            @if(is_array($carrito)) {{-- Asegurarse de que sea un carrito válido --}}
                <div class="card mb-4">
                    <div class="card-header font-bold">{{__("Servicio")}} #{{ $carrito['id'] ?? 'N/A' }}</div>
                    <div class="card-body">
                        {{-- Información del carrito principal --}}
                        <p><strong>{{__("Tipo_servicio")}}:</strong> 
                            @if(isset($carrito['Tipo_servicio']))
                                @if($carrito['Tipo_servicio'] === 'T')
                                     {{ __("Traslados") }}
                                @elseif($carrito['Tipo_servicio'] === 'TOU')
                                     {{ __("Tours") }}
                                @elseif($carrito['Tipo_servicio'] === 'H')
                                    Hotel {{ __("Hoteles") }}
                                @else
                                    {{ $carrito['Tipo_servicio'] }}
                                @endif
                            @else
                                N/A
                            @endif
                        </p>
                        <p><strong>{{ __("Monto_total") }}:</strong> 
                            @if(isset($carrito['Precio_Total']))
                                {{ $currency->formatear($carrito['Precio_Total']) }}
                            @else
                                N/A
                            @endif
                        </p>
                        <p><strong>{{__("Fecha_creacion")}}:</strong> {{ isset($carrito['created_at']) ? \Carbon\Carbon::parse($carrito['created_at'])->translatedFormat('d F Y') : 'N/A' }}</p>
                        <p><strong>{{__("Fecha_actualizacion")}}:</strong> {{ isset($carrito['updated_at']) ? \Carbon\Carbon::parse($carrito['updated_at'])->translatedFormat('d F Y') : 'N/A' }}</p>
                        {{-- Campo opcional: Email del encargado --}}
                        
                    </div>

                    {{-- Mostrar detalle del carrito --}}
                    <div class="card mt-3">
                        <div class="card-header font-bold">{{__("Detalle_servicio")}}</div>
                        <div class="card-body">
                            @if(isset($carrito['detalle']) && is_array($carrito['detalle']))
                                @if($carrito['Tipo_servicio'] === 'H')
                                    {{-- Detalles del Hotel --}}
                                    <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{__("Fecha_desde")}}</th>
                                                <th>{{__("Fecha_hasta")}}</th>
                                                <th>{{__("Tipo_habitacion")}}</th>
                                                <th>{{__("Regimen")}}</th>
                                                <th>{{__("Cantidad_adultos")}}</th>
                                                <th>{{__("Cantidad_menores")}}</th>
                                                <th>{{__("Noches")}}</th>
                                                <th>{{__("Precio_promedio_por_noche")}}</th>
                                                <th>{{__("Monto_total")}}</th>
                                                <th>{{__("Habitaciones")}}</th>
                                                <th>{{ __("Penalidad") }}</th>
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
                                                    <td>
                                                        @if(isset($detalle['Precio_promedio_por_noche']))
                                                            {{ $currency->formatear($detalle['Precio_promedio_por_noche']) }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($detalle['Precio_total_habitacion']))
                                                            {{ $currency->formatear($detalle['Precio_total_habitacion']) }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
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
                                                <th>{{__("Fecha_servicio")}}</th>
                                                <th>{{__("Hora_servicio")}}</th>
                                                <th>{{__("Cantidad_adultos")}}</th>
                                                <th>{{__("Cantidad_menores")}}</th>
                                                <th>{{__("Price_adulto")}}</th>
                                                <th>{{__("Price_menor")}}</th>
                                                <th>{{__("Price_total")}}</th>
                                                <th>{{__("Marca_modelo")}}</th>
                                                <th>{{__("Maximo_maletas")}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($carrito['detalle'] as $detalle)
                                                <tr>
                                                    <td>{{ isset($detalle['fecha_servicio']) ? \Carbon\Carbon::parse($detalle['fecha_servicio'])->translatedFormat('d F Y') : 'N/A' }}</td>
                                                    <td>{{ $detalle['hora_servicio'] ?? 'N/A' }}</td>
                                                    <td>{{ $detalle['Cantidad_Adultos'] ?? 'N/A' }}</td>
                                                    <td>{{ $detalle['Cantidad_Menores'] ?? 'N/A' }}</td>
                                                    <td>
                                                        @if(isset($detalle['Precio_Adulto']))
                                                            {{ $currency->formatear($detalle['Precio_Adulto']) }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($detalle['Precio_Menor']))
                                                            {{ $currency->formatear($detalle['Precio_Menor']) }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($detalle['Precio_Total']))
                                                            {{ $currency->formatear($detalle['Precio_Total']) }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>{{ isset($detalle['empresa_traslado_tipo_movilidade']['Marca_modelo']) ? $detalle['empresa_traslado_tipo_movilidade']['Marca_modelo'] : 'N/A' }}</td>
                                                    <td>{{ isset($detalle['empresa_traslado_tipo_movilidade']['Maletas_maximo']) ? $detalle['empresa_traslado_tipo_movilidade']['Maletas_maximo'] : 'N/A' }}</td>
                                                </tr>
                                                {{-- Mostrar información del servicio traslado si existe --}}
                                                @if(isset($detalle['servicio_traslado']))
                                                    <tr>
                                                        <td colspan="9">
                                                            <strong>{{__("Servicio")}}:</strong> {{ $detalle['servicio_traslado']['Nombre_Servicio'] ?? 'N/A' }} <br>
                                                            <strong>{{__("Details")}}:</strong> {{ $detalle['servicio_traslado']['Detalle_servicio'] ?? 'N/A' }} <br>
                                                            <strong>{{__("Tipo_servicio")}}:</strong> {{ $detalle['servicio_traslado']['Tipo_servicio_transfer'] ?? 'N/A' }}
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
                                                <th>{{__("Tours")}}</th>
                                                <th>{{__("Fecha_desde")}}</th>
                                                <th>{{__("Fecha_hasta")}}</th>
                                                <th>{{__("Cantidad_adultos")}}</th>
                                                <th>{{__("Cantidad_menores")}}</th>
                                                <th>{{__("Price_adulto")}}</th>
                                                <th>{{__("Price_menor")}}</th>
                                                <th>{{__("Price_total")}}</th>
                                                <th>{{__("Duracion")}}</th>
                                                <th>{{__("Recojo_hotel")}}</th>
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
                                                    <td>
                                                        @if(isset($detalle['Precio_Adulto']))
                                                            {{ $currency->formatear($detalle['Precio_Adulto']) }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($detalle['Precio_Menor']))
                                                            {{ $currency->formatear($detalle['Precio_Menor']) }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($detalle['Precio_Total']))
                                                            {{ $currency->formatear($detalle['Precio_Total']) }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>{{ $detalle['tour']['cantidad_dias_tour'] ?? 'N/A' }} días / {{ $detalle['tour']['cantidad_noches_tour'] ?? 'N/A' }} noches</td>
                                                    <td>{{ $detalle['tour']['Recojo_hotel'] == 1 ? 'Sí' : 'No' }}</td>
                                                </tr>
                                                {{-- Mostrar información adicional del tour si existe --}}
                                                @if(isset($detalle['tour']))
                                                    <tr>
                                                        <td colspan="10">
                                                            <strong>{{__("Pais")}}:</strong> {{ $detalle['tour']['pais']['Nombre_Pais'] ?? 'N/A' }} <br>
                                                            <strong>{{__("Ciudad")}}:</strong> {{ $detalle['tour']['ciudad']['Nombre_Ciudad'] ?? 'N/A' }} <br>
                                                            <strong>{{__("Zona")}}:</strong> {{ $detalle['tour']['zona']['Nombre_Zona'] ?? 'N/A' }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            @else
                                <p>{{__("No_hay_respuesta")}}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endforeach    


    
        <!-- Muesta el contador de en cuanto exprira el carrito-->
        <div class="countdown-container">
            <div class="countdown-text" id="countdown-text"></div>
            <div class="progress" style="height: 10px; margin-top: 10px;">
                <div id="countdown-progress" class="progress-bar bg-success" role="progressbar" 
                    style="width: 100%; transition: width 1s linear;"></div>
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
                    
                    // Calcular porcentaje del tiempo RESTANTE (esto es lo que debe disminuir)
                    const remainingPercentage = Math.max(0, (remainingTime / totalDuration) * 100);
                    
                    // Seleccionar elementos
                    const progressBar = document.getElementById('countdown-progress');
                    const countdownText = document.getElementById('countdown-text');
                    
                    if (remainingTime <= 0) {
                        countdownText.innerHTML = "¡Tiempo agotado!";
                        progressBar.style.width = '0%';
                        progressBar.className = 'progress-bar bg-danger';
                        
                        // Recargar la página
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                        
                        return;
                    }

                    // Convertir segundos a horas, minutos y segundos
                    const hours = Math.floor(remainingTime / 3600);
                    const minutes = Math.floor((remainingTime % 3600) / 60);
                    const seconds = remainingTime % 60;

                    // Actualizar texto
                    countdownText.innerHTML = `Tiempo restante: ${hours}h ${minutes}m ${seconds}s`;

                    // Actualizar barra de progreso - el ancho es el porcentaje de tiempo RESTANTE
                    progressBar.style.width = `${remainingPercentage}%`;
                    
                    // Cambiar colores según el tiempo restante
                    if (remainingPercentage < 30) { // Menos del 30% del tiempo restante
                        progressBar.className = 'progress-bar bg-danger';
                    } else if (remainingPercentage < 60) { // Menos del 60% del tiempo restante
                        progressBar.className = 'progress-bar bg-warning';
                    } else {
                        progressBar.className = 'progress-bar bg-success';
                    }

                    // Actualizar cada segundo
                    setTimeout(updateCountdown, 1000);
                }

                // Iniciar el contador
                updateCountdown();
            </script>
            <div class="alert alert-info">
                <strong>Precio Total del Carrito:</strong> 
                {{ $currency->formatear($respuestas['Precio_total_carrito']) }}
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
                            <input type="text" name="Nombre_titular_reserva" value="{{ old('Nombre_titular_reserva') }}" id="Nombre_titular_reserva" class="form-control rounded-pill px-4"  maxlength="30"  pattern="[A-Za-zñÑ\s]+" oninput="this.value = this.value.replace(/[^A-Za-zñÑ\s]/g, '')" placeholder="Titular que viaja" required>
                        </div>
                        <div class="row md-6">
                            <label for="Apellido_titular_reserva">Apellido del Titular de la Reserva</label>
                            <input type="text" name="Apellido_titular_reserva" value="{{ old('Apellido_titular_reserva') }}" id="Apellido_titular_reserva" class="form-control rounded-pill px-4"  maxlength="30" pattern="[A-Za-zñÑ\s]+" oninput="this.value = this.value.replace(/[^A-Za-zñÑ\s]/g, '')" placeholder="Apellido del titular que viaja" required>
                        </div>
                        <div class="row md-6">
                            <label for="Telefono_titular_reserva">Telefono del Titular de la Reserva</label>
                            <input type="text" name="Telefono_titular_reserva"  value="{{ old('Telefono_titular_reserva') }}" id="Telefono_titular_reserva" class="form-control rounded-pill px-4" pattern="^[+\d\s]+$" oninput="this.value = this.value.replace(/[^+\d\s]/g, '')" minlength="8" maxlength="20" placeholder="Ej.: +59 12345678" required>
                        </div>
                        <div class="row mb-3">
                            <label for="Email_contacto_reserva">Email de Contacto</label>
                            <input type="email" name="Email_contacto_reserva" value="{{ old('Email_contacto_reserva') }}" id="Email_contacto_reserva" class="form-control rounded-pill px-4" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" title="Ejemplo: usuario@dominio.com" placeholder="Ej.: usuario@dominio.com" required>
                        </div>
                         <div class="row mb-4">
                            <label for="Comentarios" class="col-md-6">RUC (Opcional)</label>
                            <input name="ruc" value="{{ old('ruc') }}"  id="ruc" class="form-control rounded-pill px-4" class="form-control rounded-pill px-4" pattern="^[0-9-]{1,15}$" oninput="this.value = this.value.replace(/[^0-9-]/g, '').slice(0,15)" maxlength="15" minlength="6"   placeholder="Ej.: 12345678-9">
                        </div>
                        <div class="row mb-4">
                            <label for="nombre_ruc" class="col-md-6">Nombre RUC (Opcional)</label>
                            <input name="nombre_ruc" value="{{ old('nombre_ruc') }}"  id="nombre_ruc" class="form-control rounded-pill px-4" pattern="^[A-Za-z0-9 ]{1,50}$" oninput="this.value = this.value.replace(/[^A-Za-z0-9 ]/g, '').slice(0,50)" maxlength="50"  placeholder="Nombre o Razón Social">
                        </div>
                        <div class="row mb-4">
                            <label for="Comentarios" class="col-md-6">Comentarios (Opcional)</label>
                            <textarea name="Comentarios" id="Comentarios" class="form-control rounded-pill px-4 py-2" rows="3" pattern="[A-Za-zñÑ0-9\s\.,]+" oninput="this.value = this.value.replace(/[^A-Za-zñÑ0-9\s.,]/g, '')">{{ old('Comentarios') }}</textarea>
                        </div>
                        
                        @php
                            // Calcular el monto en guaraníes para mostrar
                           $tasaCambioGuaranies = $currency->obtenerTasa('USD', 'PYG');
                           
                            $montoGuaranies = $respuestas['Precio_total_carrito'] * $tipoCambioGuaranies;
                        @endphp
                        
                        <button type="button" id="btnMostrarQR" class=" btn-crypto text-white px-4">
                            <i class="bi bi-qr-code me-2"></i> 
                             {{ $currency->formatear($respuestas['Precio_total_carrito']) }} (Cripto)
                        </button>

                        <button type="button" id="btnMostrarTarjeta" class=" btn-card text-white px-4">
                            <i class="bi bi-credit-card me-2"></i> 
                            Tarjeta {{ $currency->formatear($respuestas['Precio_total_carrito']) }}  
                            → {{ number_format($montoGuaranies, 0, '.', ',') }} (Gs.)
                        </button>
                        
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

                <!-- Modal Tarjeta -->
                <div class="modal fade" id="tarjetaModal" tabindex="-1" aria-labelledby="tarjetaModalLabel" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="tarjetaModalLabel">Pago con Tarjeta</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-0" style="height: 500px;">
                                <div id="bancard-iframe-container"></div>
                                <div id="loading-spinner" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p class="mt-2">Cargando pasarela de pago...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fin Modal Tarjeta -->

            </div>
        @endif
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
        const status = "{{ request('status') }}"; // Captura ?status=value desde la URL
        
        if (status === "payment_success") {
                   
          // Verificación de estado
                (async () => {
                    const pago_id = new URLSearchParams(window.location.search).get('id_pago');
                    
                    const statusResponse = await fetch(`/pagos/verificar-estado/${pago_id}`);
                    
                    const statusData = await statusResponse.json();
                    //alert(statusData.status);
                    if (statusData.status === 'PAGADO') {
                        
                        document.getElementById('formReserva').submit();
                    }else{
                     
                        alert(`Pago fallido: ${statusData.message || 'Error desconocido'}`);
                    }
                })();
        }//fin del si el pago es satisfactorio
        else if (status === "payment_fail") {
            // Mostrar modal de carga inicial
            showModalErrorPago('Verificando pago', 'Estamos validando el estado de tu transacción...', 'loading');
            
            // Función async autoinvocada
            (async () => {
                try {
                    const pago_id = new URLSearchParams(window.location.search).get('pago_id');
                    const response = await fetch(`/pagos/verificar-estado/${pago_id}`);
                    const statusData = await response.json();
                    
                    // Cerrar modal
                    closePaymentErrorModal();
                    
                    if (statusData.status === 'PAGADO') {
                        document.getElementById('formReserva').submit();
                    } else {
                        // Mostrar modal de error específico
                        const errorMessage = getPaymentErrorMessage(statusData.status);
                        showModalErrorPago('Error en el pago', errorMessage, 'error');
                    }
                } catch (error) {
                    closePaymentErrorModal();
                    showModalErrorPago('Error de conexión', 'No pudimos verificar tu pago. Por favor intenta nuevamente.', 'error');
                    console.error("Error:", error);
                }
            })();
        }

        // Función para mostrar modal de error
        function showModalErrorPago(title, message, type) {
            // Crear o obtener el modal
            const modal = createPaymentErrorModal();
            
            // Configurar contenido
            modal.querySelector('.payment-modal-title').textContent = title;
            modal.querySelector('.payment-modal-body').innerHTML = `
                <div style="font-size:4em; margin-bottom:20px;">${getPaymentIcon(type)}</div>
                <p style="font-size:1.1em; color:#555;">${message}</p>
            `;
            
            // Mostrar modal
            modal.style.display = 'block';
            
            // Bloquear scroll del body
            document.body.style.overflow = 'hidden';
        }
        // Crear modal de error
        function createPaymentErrorModal() {
            // Verificar si ya existe
            if (document.getElementById('paymentModalError')) {
                return document.getElementById('paymentModalError');
            }
            
            // Crear estructura COMPLETA del modal
            const modalHTML = `
            <div id="paymentModalError" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.7); z-index:9999;">
                <div style="position:relative; background:white; margin:5% auto; padding:25px; width:90%; max-width:600px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.3);">
                    <span class="payment-close-btn" style="position:absolute; top:15px; right:20px; font-size:1.8em; color:#aaa; cursor:pointer;">×</span>
                    <h3 class="payment-modal-title" style="color:#d32f2f; margin-bottom:20px; text-align:center;"></h3>
                    <div class="payment-modal-body" style="text-align:center; padding:10px;"></div>
                </div>
            </div>
            `;
            
            // Insertar en el body
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            
            // Agregar evento para cerrar
            document.querySelector('.payment-close-btn').addEventListener('click', closePaymentErrorModal);
            
            return document.getElementById('paymentModalError');
        }

        // Cerrar modal
        function closePaymentErrorModal() {
            const modal = document.getElementById('paymentModalError');
            if (modal) {
                modal.style.display = 'none';
                // Restaurar scroll del body
                document.body.style.overflow = 'auto';
            }
        }

        // Obtener icono
        function getPaymentIcon(type) {
            const icons = {
                loading: '⏳',
                success: '✅',
                error: '❌'
            };
            return icons[type] || '!';
        }

        // Mensajes de error específicos
        function getPaymentErrorMessage(status) {
            const messages = {
                'FONDOS_INSUFICIENTES': 'No hay fondos suficientes en tu cuenta',
                'TARJETA_RECHAZADA': 'Tu tarjeta fue rechazada por el banco',
                'TIMEOUT': 'La operación tardó demasiado',
                'default': 'Hubo un problema al procesar tu pago'
            };
            return messages[status] || messages['default'];
        }
       // Configuración para Bancard (sin cambios)
        const bancardConfig = {
            publicKey: '{{ env("BANCARD_PUBLIC_KEY") }}',
            checkoutScript: '{{ env("BANCARD_BASE_URL") }}/checkout/javascript/dist/bancard-checkout-4.0.0.js'
        };

        // Cargar SDK Bancard dinámicamente (sin cambios)
        function loadBancardSDK() {
            return new Promise((resolve, reject) => {
                if (window.Bancard) {
                    resolve();
                    return;
                }
                const script = document.createElement('script');
                script.src = bancardConfig.checkoutScript;
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        }

        // 👇 Nueva función para renderizar formulario 3DS
        function render3DSForm(processId, modal) {
            const styles = {
                "button-background-color": "#4faed1",
                "input-text-color": "#333"
            };
            
            Bancard.Charge3DS.createForm(
                'bancard-iframe-container',
                processId,
                styles
            );
        }

        // 👇 Modificación principal en el evento de pago
        document.getElementById('btnMostrarTarjeta').addEventListener('click', async function() {
            const form = document.getElementById('formReserva');
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            guardarDatosFormulario();

            const modal = new bootstrap.Modal(document.getElementById('tarjetaModal'));
            modal.show();

            try {
                await loadBancardSDK();
                //alert("hola");
                const response = await fetch("{{ route('pagos.iniciar') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        amount: document.getElementById('montoPago').value,
                        description: "Pago de reserva",
                        // 👇 Asegúrate que tu backend use esto para habilitar 3DS
                        force_3ds: true 
                    })
                });
            
                const data = await response.json();
                //alert(data.process_id);
                
                if (data.status !== 'success') {
                    throw new Error(data.message || 'Error al iniciar pago');
                }

                document.getElementById('loading-spinner').style.display = 'none';

                // 👇 Decide qué formulario cargar basado en la respuesta
                if (data.requires_3ds) {
                    render3DSForm(data.process_id, modal);
                } else {
                    const styles = {
                        "form-background-color": "#f8f9fa",
                        "button-background-color": "#4faed1"
                    };
                    Bancard.Checkout.createForm(
                        'bancard-iframe-container', 
                        data.process_id, 
                        styles
                    );
                }

                

            } catch (error) {
                console.error('Error:', error);
                modal.hide();
                alert('Error al procesar el pago: ' + error.message);
            }
        });
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
        // NUEVOS CAMPOS
        localStorage.setItem('ruc', document.getElementById('ruc').value);
        localStorage.setItem('nombre_ruc', document.getElementById('nombre_ruc').value);
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
            'Comentarios',
            'ruc',
            'nombre_ruc'
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