<!-- Detalle del Tour -->
<div class="card shadow mb-4">
    <div class="card-header bg-success text-white">
        <h2 class="h5 mb-0">Detalle del Tour</h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>{{__("Tours")}}:</strong> {{ $data['detalle_tour']['tour']['Nombre_tour'] }}</p>
                <p><strong>{{__("Email_contacto")}}:</strong> {{ $data['detalle_tour']['tour']['Email_contacto_tour'] }}</p>
                <p><strong>{{__("Duracion")}}:</strong> {{ $data['detalle_tour']['tour']['cantidad_dias_tour'] }} días / {{ $data['detalle_tour']['tour']['cantidad_noches_tour'] }} noches</p>
                <p><strong>{{__("Punto_encuentro")}}:</strong> {{ $data['detalle_tour']['tour']['Punto_encuentro'] }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>{{__("Fecha_desde")}}:</strong> {{ \Carbon\Carbon::parse($data['detalle_tour']['Fecha_In'])->format('d/m/Y') }}</p>
                <p><strong>{{__("Fecha_hasta")}}:</strong> {{ \Carbon\Carbon::parse($data['detalle_tour']['Fecha_Out'])->format('d/m/Y') }}</p>
                <p><strong>{{__("Price_adulto")}}:</strong> ${{ number_format($data['detalle_tour']['Precio_Adulto'], 2) }}</p>
                <p><strong>{{__("Price_menor")}}:</strong> ${{ number_format($data['detalle_tour']['Precio_Menor'], 2) }}</p>
                <p><strong>{{__("Price_total")}}:</strong> ${{ number_format($data['detalle_tour']['Precio_Total'], 2) }}</p>
            </div>
        </div>
        <hr>
        <h5>{{__("Descripcion")}}:</h5>
        <div>{!! $data['detalle_tour']['tour']['Detalle_tour'] !!}</div>
    </div>
</div>