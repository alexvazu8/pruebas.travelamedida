<!-- Tarjeta de Detalles del Hotel -->
<div class="card shadow mb-4">
    <div class="card-header bg-info text-white">
        <h2 class="h5 mb-0">Detalles del Alojamiento</h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h4 class="mb-3">Información de la Reserva</h4>
                <p><strong>Habitación:</strong> {{ $data['detalle_hotel']['Nombre_Habitacion'] }}</p>
                <p><strong>Cantidad de Habitaciones:</strong> {{ $data['detalle_hotel']['Cantidad_habitaciones'] }}</p> 
                <p><strong>Régimen:</strong> {{ $data['detalle_hotel']['Nombre_Regimen'] }}</p>
                <p><strong>Adultos:</strong> {{ $data['detalle_hotel']['Cantidad_Adultos'] }}</p>
                <p><strong>Menores:</strong> {{ $data['detalle_hotel']['Cantidad_Menores'] }}</p>
                <p><strong>Noches:</strong> {{ $data['detalle_hotel']['Cantidad_Noches'] }}</p>
                <p><strong>Check-in:</strong> {{ \Carbon\Carbon::parse($data['detalle_hotel']['Fecha_In'])->format('d/m/Y') }}</p>
                <p><strong>Check-out:</strong> {{ \Carbon\Carbon::parse($data['detalle_hotel']['Fecha_Out'])->format('d/m/Y') }}</p>
                <p><strong>Precio Total:</strong> ${{ number_format($data['detalle_hotel']['Precio_Total'], 2) }}</p>
            </div>
            
            <div class="col-md-6">
                <h4 class="mb-3">Información del Hotel</h4>
                <p><strong>Hotel:</strong> {{ $data['detalle_hotel']['tipo_habitacion_hotel']['hotel']['Nombre_Hotel'] }}</p>
                <p><strong>Dirección:</strong> {{ $data['detalle_hotel']['tipo_habitacion_hotel']['hotel']['Direccion_Hotel'] }}</p>
                <!-- Mapa pequeño -->
                <div class="map-container">
                    <div id="miniMapa"></div>
                    <small class="text-muted">Ubicación aproximada del hotel</small>
                </div>
                <p><strong>Teléfono:</strong> {{ $data['detalle_hotel']['tipo_habitacion_hotel']['hotel']['Telefono_reservas_hotel'] }}</p>
                <p><strong>Celular:</strong> {{ $data['detalle_hotel']['tipo_habitacion_hotel']['hotel']['Cel_reservas_hotel'] }}</p>
                <p><strong>Email Reservas:</strong> {{ $data['detalle_hotel']['tipo_habitacion_hotel']['hotel']['email_reservas_hotel'] }}</p>
                <p><strong>Email Comercial:</strong> {{ $data['detalle_hotel']['tipo_habitacion_hotel']['hotel']['email_comercial_hotel'] }}</p>
                <p><strong>Descripción:</strong> {{ $data['detalle_hotel']['tipo_habitacion_hotel']['hotel']['Descripcion_Hotel'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Tarjeta de Políticas -->
<div class="card shadow">
    <div class="card-header bg-warning">
        <h2 class="h5 mb-0">Políticas de Cancelación</h2>
    </div>
    <div class="card-body">
        <p><strong>Política:</strong> {{ $data['detalle_hotel']['politica']['Nombre_Politica'] }}</p>
        
        <h4 class="mt-4">Penalidades por Cancelación</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Penalidad</th>
                        <th>Desde (noches antes)</th>
                        <th>Hasta (noches antes)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['detalle_hotel']['politica']['penalidads'] as $penalidad)
                    <tr>
                        <td>{{ $penalidad['porcentaje_penalidad_por_noche'] }}%</td>
                        <td>{{ $penalidad['desde_noches_antes'] }}</td>
                        <td>{{ $penalidad['hasta_noches_antes'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>