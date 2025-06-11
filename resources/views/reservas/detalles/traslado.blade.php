<!-- Tarjeta de Detalles del Traslado -->
<div class="card shadow mb-4">
    <div class="card-header bg-info text-white">
        <h2 class="h5 mb-0">Detalles del Traslado</h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h4 class="mb-3">Información del Servicio</h4>
                <p><strong>Servicio:</strong> {{ $data['detalle_traslado']['servicio_traslado']['Nombre_Servicio'] }}</p>
                <p><strong>Tipo de Servicio:</strong> {{ $data['detalle_traslado']['servicio_traslado']['Tipo_servicio_transfer'] == 'IN' ? 'Ingreso' : 'Salida' }}</p>
                <p><strong>Detalle:</strong> {{ $data['detalle_traslado']['servicio_traslado']['Detalle_servicio'] }}</p>
                <p><strong>Adultos:</strong> {{ $data['detalle_traslado']['Cantidad_Adultos'] }}</p>
                <p><strong>Menores:</strong> {{ $data['detalle_traslado']['Cantidad_Menores'] }}</p>
                <p><strong>Precio Adulto:</strong> ${{ number_format($data['detalle_traslado']['Precio_Adulto'], 2) }}</p>
                <p><strong>Precio Menor:</strong> ${{ number_format($data['detalle_traslado']['Precio_Menor'], 2) }}</p>
                <p><strong>Precio Total:</strong> ${{ number_format($data['detalle_traslado']['Precio_Total'], 2) }}</p>
            </div>
            
            <div class="col-md-6">
                <h4 class="mb-3">Detalles del Viaje</h4>
                <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($data['detalle_traslado']['fecha_servicio'])->format('d/m/Y') }}</p>
                <p><strong>Hora:</strong> {{ $data['detalle_traslado']['hora_servicio'] }}</p>
                <p><strong>Origen:</strong> {{ $data['detalle_traslado']['Lugar_Origen'] }}</p>
                <p><strong>Zona Origen:</strong> {{ $data['detalle_traslado']['servicio_traslado']['zona']['Nombre_Zona'] }}</p>
                <p><strong>Destino:</strong> {{ $data['detalle_traslado']['Lugar_Destino'] }}</p>
                <p><strong>Zona Destino:</strong> {{ $data['detalle_traslado']['servicio_traslado']['zona_destino']['Nombre_Zona'] }}</p>
                
                <h4 class="mt-4">Información del Vehículo</h4>
                <p><strong>Tipo de Movilidad:</strong> {{ $data['detalle_traslado']['servicio_traslado']['empresa_traslado_tipo_movilidade']['tipo_movilidad']['Nombre_tipo_movilidad'] }}</p>
                <p><strong>Modelo:</strong> {{ $data['detalle_traslado']['servicio_traslado']['empresa_traslado_tipo_movilidade']['Marca_modelo'] }}</p>
                <p><strong>Capacidad Máxima:</strong> {{ $data['detalle_traslado']['servicio_traslado']['empresa_traslado_tipo_movilidade']['Numero_max_pasajeros'] }} pasajeros</p>
                <p><strong>Maletas Máximas:</strong> {{ $data['detalle_traslado']['servicio_traslado']['empresa_traslado_tipo_movilidade']['Maletas_maximo'] }}</p>
            </div>
        </div>
    </div>
</div>