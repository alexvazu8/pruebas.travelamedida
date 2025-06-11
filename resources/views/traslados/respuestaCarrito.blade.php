@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Respuestas del Traslado</h1>
    @php 
    //print_r($respuestas);
    @endphp
    @if(isset($respuestas['error']))
        @php 
        print_r($respuestas);
        @endphp
          {{$respuestas['error']}}
          <a href="{{ url('/traslados') }}" class="btn btn-primary btn-lg mx-2">Traslados</a>
    @else
        @if(empty($respuestas))
            <p>No hay respuestas disponibles.</p>
           
            <a href="{{ url('/traslados') }}" class="btn btn-primary btn-lg mx-2">Traslados</a>
        
        @else
           
       
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Campo</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Hora de Servicio</td>
                            <td>{{ $respuestas['hora_servicio'] }}</td>
                        </tr>
                        <tr>
                            <td>Precio Adulto</td>
                            <td>{{ $respuestas['Precio_Adulto'] }}</td>
                        </tr>
                        <tr>
                            <td>Precio Menor</td>
                            <td>{{ $respuestas['Precio_Menor'] }}</td>
                        </tr>
                        <tr>
                            <td>Cantidad de Adultos</td>
                            <td>{{ $respuestas['Cantidad_Adultos'] }}</td>
                        </tr>
                        <tr>
                            <td>Cantidad de Menores</td>
                            <td>{{ $respuestas['Cantidad_Menores'] }}</td>
                        </tr>
                        <tr>
                            <td>Precio Total</td>
                            <td>{{ $respuestas['Precio_Total'] }}</td>
                        </tr>
                        <tr>
                            <td>Tipo de Movilidad</td>
                            <td>{{ $respuestas['Empresa_traslados_tipo_movilidades_id'] }}</td>
                        </tr>
                        <tr>
                            <td>Fecha de Servicio</td>
                            <td>{{ $respuestas['fecha_servicio'] }}</td>
                        </tr>
                        <tr>
                            <td>ID Carrito</td>
                            <td>{{ $respuestas['carrito_compras_items_id'] }}</td>
                        </tr>
                        <tr>
                            <td>Última Actualización</td>
                            <td>{{ $respuestas['updated_at'] }}</td>
                        </tr>
                        <tr>
                            <td>Creado en</td>
                            <td>{{ $respuestas['created_at'] }}</td>
                        </tr>
                        <tr>
                            <td>ID</td>
                            <td>{{ $respuestas['id'] }}</td>
                        </tr>
                    </tbody>
                </table>
                <a href="{{ url('/traslados') }}" class="btn btn-primary">Volver a Traslados</a>    
       
        @endif
    @endif
</div>
@endsection
