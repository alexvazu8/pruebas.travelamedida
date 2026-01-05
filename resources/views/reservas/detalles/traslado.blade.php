<!-- Tarjeta de Detalles del Traslado -->
<div class="card shadow mb-4">
    <div class="card-header bg-info text-white">
        <h2 class="h5 mb-0">{{__("Detalle_traslado")}}</h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h4 class="mb-3">{{__("Informacion_servicio")}}</h4>
                <p><strong>{{__("Servicio")}}:</strong> {{ $data['detalle_traslado']['servicio_traslado']['Nombre_Servicio'] }}</p>
                <p><strong>{{__("Tipo_servicio")}}:</strong> {{ $data['detalle_traslado']['servicio_traslado']['Tipo_servicio_transfer'] == 'IN' ? 'Ingreso' : 'Salida' }}</p>
                <p><strong>{{__("Details")}}:</strong> {{ $data['detalle_traslado']['servicio_traslado']['Detalle_servicio'] }}</p>
                <p><strong>{{__("Cantidad_adultos")}}:</strong> {{ $data['detalle_traslado']['Cantidad_Adultos'] }}</p>
                <p><strong>{{__("Cantidad_menores")}}:</strong> {{ $data['detalle_traslado']['Cantidad_Menores'] }}</p>
                <p><strong>{{__("Price_adulto")}}:</strong> ${{ number_format($data['detalle_traslado']['Precio_Adulto'], 2) }}</p>
                <p><strong>{{__("Price_menor")}}:</strong> ${{ number_format($data['detalle_traslado']['Precio_Menor'], 2) }}</p>
                <p><strong>{{__("Price_total")}}:</strong> ${{ number_format($data['detalle_traslado']['Precio_Total'], 2) }}</p>
            </div>
            
            <div class="col-md-6">
                <h4 class="mb-3">{{__("Detalle_traslado")}}</h4>
                <p><strong>{{__("Fecha_servicio")}}:</strong> {{ \Carbon\Carbon::parse($data['detalle_traslado']['fecha_servicio'])->format('d/m/Y') }}</p>
                <p><strong>{{__("Hora_servicio")}}:</strong> {{ $data['detalle_traslado']['hora_servicio'] }}</p>
                <p><strong>{{__("Lugar_origen")}}:</strong> {{ $data['detalle_traslado']['Lugar_Origen'] }}</p>
                <p><strong>{{__("Zona_origen")}}:</strong> {{ $data['detalle_traslado']['servicio_traslado']['zona']['Nombre_Zona'] }}</p>
                <p><strong>{{__("Lugar_destino")}}:</strong> {{ $data['detalle_traslado']['Lugar_Destino'] }}</p>
                <p><strong>{{__("Zona_destino")}}:</strong> {{ $data['detalle_traslado']['servicio_traslado']['zona_destino']['Nombre_Zona'] }}</p>
                
                <h4 class="mt-4">{{__("Vehicle")}}</h4>
                <p><strong>{{__("Tipo_vehiculo")}}:</strong> {{ $data['detalle_traslado']['servicio_traslado']['empresa_traslado_tipo_movilidade']['tipo_movilidad']['Nombre_tipo_movilidad'] }}</p>
                <p><strong>{{__("Marca_modelo")}}:</strong> {{ $data['detalle_traslado']['servicio_traslado']['empresa_traslado_tipo_movilidade']['Marca_modelo'] }}</p>
                <p><strong>{{__("Maxima_capacidad")}}:</strong> {{ $data['detalle_traslado']['servicio_traslado']['empresa_traslado_tipo_movilidade']['Numero_max_pasajeros'] }} pasajeros</p>
                <p><strong>{{__("Maximo_maletas")}}:</strong> {{ $data['detalle_traslado']['servicio_traslado']['empresa_traslado_tipo_movilidade']['Maletas_maximo'] }}</p>
            </div>
        </div>
    </div>
</div>