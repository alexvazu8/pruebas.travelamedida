<!-- Tarjeta de Detalles del Hotel -->
<div class="card shadow mb-4">
    <div class="card-header bg-info text-white">
        <h2 class="h5 mb-0">{{__("Detalle_hotel")}}</h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h4 class="mb-3">{{__("Informacion_reservas")}}</h4>
                <p><strong>{{__("Habitacion")}}:</strong> {{ $data['detalle_hotel']['Nombre_Habitacion'] }}</p>
                <p><strong>{{__("Habitaciones")}}:</strong> {{ $data['detalle_hotel']['Cantidad_habitaciones'] }}</p> 
                <p><strong>{{__("Regimen")}}:</strong> {{ $data['detalle_hotel']['Nombre_Regimen'] }}</p>
                <p><strong>{{__("Cantidad_adultos")}}:</strong> {{ $data['detalle_hotel']['Cantidad_Adultos'] }}</p>
                <p><strong>{{__("Cantidad_menores")}}:</strong> {{ $data['detalle_hotel']['Cantidad_Menores'] }}</p>
                <p><strong>{{__("Noches")}}:</strong> {{ $data['detalle_hotel']['Cantidad_Noches'] }}</p>
                <p><strong>{{__("Fecha_desde")}}:</strong> {{ \Carbon\Carbon::parse($data['detalle_hotel']['Fecha_In'])->format('d/m/Y') }}</p>
                <p><strong>{{__("Fecha_hasta")}}:</strong> {{ \Carbon\Carbon::parse($data['detalle_hotel']['Fecha_Out'])->format('d/m/Y') }}</p>
                <p><strong>{{__("Price_total")}}:</strong> ${{ number_format($data['detalle_hotel']['Precio_Total'], 2) }}</p>
            </div>
            
            <div class="col-md-6">
                <h4 class="mb-3">{{ __("Informacion_hotel") }}</h4>
                <p><strong>{{ __("Nombre_hotel") }}:</strong> {{ $data['detalle_hotel']['tipo_habitacion_hotel']['hotel']['Nombre_Hotel'] }}</p>
                <p><strong>{{ __("Direccion") }}:</strong> {{ $data['detalle_hotel']['tipo_habitacion_hotel']['hotel']['Direccion_Hotel'] }}</p>
                <!-- Mapa pequeño -->
                <div class="map-container">
                    <div id="miniMapa"></div>
                    <small class="text-muted">{{__("Location")}}</small>
                </div>
                <p><strong>{{__("Telefono")}}:</strong> {{ $data['detalle_hotel']['tipo_habitacion_hotel']['hotel']['Telefono_reservas_hotel'] }}</p>
                <p><strong>{{__("Celular")}}:</strong> {{ $data['detalle_hotel']['tipo_habitacion_hotel']['hotel']['Cel_reservas_hotel'] }}</p>
                <p><strong>{{__("Email_reservas")}}:</strong> {{ $data['detalle_hotel']['tipo_habitacion_hotel']['hotel']['email_reservas_hotel'] }}</p>
                <p><strong>{{__("Email_comercial")}}:</strong> {{ $data['detalle_hotel']['tipo_habitacion_hotel']['hotel']['email_comercial_hotel'] }}</p>
                <p><strong>{{__("Descripcion")}}:</strong> {{ $data['detalle_hotel']['tipo_habitacion_hotel']['hotel']['Descripcion_Hotel'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Tarjeta de Políticas -->
<div class="card shadow">
    <div class="card-header bg-warning">
        <h2 class="h5 mb-0">{{__("Politica_cancelacion")}}</h2>
    </div>
    <div class="card-body">
        <p><strong>{{__("Politica_cancelacion")}}:</strong> {{ $data['detalle_hotel']['politica']['Nombre_Politica'] }}</p>
        
        <h4 class="mt-4">{{__("Penalidad")}}</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>{{__("Penalidad")}}</th>
                        <th>Desde (noches antes)</th>
                        <th>Hasta (noches antes)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['detalle_hotel']['politica']['penalidads'] as $penalidad)
                    <tr>
                        <td>{{ $penalidad['porcentaje_penalidad_por_noche'] }}</td>
                        <td>{{ $penalidad['desde_noches_antes'] }}</td>
                        <td>{{ $penalidad['hasta_noches_antes'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>