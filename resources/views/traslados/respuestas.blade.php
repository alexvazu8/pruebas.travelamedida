@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Respuestas del Traslado</h1>
    @php
    //print_r($respuestas);
    @endphp
    @if(empty($respuestas))
    <p>No hay respuestas para mostrar</p>
    <a href="{{ url('/traslados') }}" class="btn btn-primary btn-lg mx-2">Traslados</a>
    @else
        @if(isset($respuestas['error']))
            {{$respuestas['error']}}
            @if(isset($respuestas['validation_errors']))
                @foreach ($respuestas['validation_errors'] as $error)
                    @foreach ($error as $detelle_error)
                        <p>{{ $detelle_error }}</p>
                    @endforeach
                @endforeach
            @endif
            <a href="{{ url('/traslados') }}" class="btn btn-primary btn-lg mx-2">Traslados</a>
        @else
            <!-- Tabla para mostrar los datos de las respuestas -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Campo</th>
                        <th>Valor</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @foreach($respuestas as $index => $respuesta)
                        <!-- Fila 1: Nombre del Servicio -->
                        <tr class="{{ is_numeric($index) && $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                            <td><strong>Nombre Servicio</strong></td>
                            <td>{{ $respuesta['Nombre_Servicio'] }}</td>
                        </tr>

                        <!-- Fila 2: Detalle del Servicio -->
                        <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                            <td><strong>Detalle del Servicio</strong></td>
                            <td colspan="2">{{ $respuesta['Detalle_servicio'] }}</td>
                        </tr>

                        <!-- Fila 3: Formulario con campos y botón -->
                        <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                            <td><strong>Origen/Destino</strong></td>
                            <td colspan="2">
                                <form id="form-{{ $index }}" action="{{ route('traslados.addCarrito') }}" method="POST">
                                    @csrf
                                    <!-- Campos ocultos -->
                                    <input type="hidden" name="Traslados_contrato_id" value="{{ $respuesta['Traslados_contrato_id'] }}">
                                    <input type="hidden" name="Tipo_movilidad_id" value="{{ $respuesta['Tipo_movilidad_id'] }}">
                                    <input type="hidden" name="Id_servicio_traslado" value="{{ $respuesta['Id_servicio_traslado'] }}">
                                    <input type="hidden" name="Fecha_disponible" value="{{ $respuesta['Fecha_disponible'] }}">
                                    <input type="hidden" name="Tipo_servicio" value="{{ $respuesta['Tipo_servicio'] }}">
                                    <input type="hidden" name="Tipo_servicio_transfer" value="{{ $respuesta['Tipo_servicio_transfer'] }}">
                                    <input type="hidden" name="hora_servicio" value="{{ $respuesta['hora_servicio'] }}">
                                    <input type="hidden" name="Numero_adultos" value="{{ $respuesta['Cantidad_adultos'] }}">
                                    <input type="hidden" name="Numero_menores" value="{{ $respuesta['Cantidad_menores'] }}">
                                    
                                    @if(isset($respuesta['Edad_menores']))
                                        @foreach ($respuesta['Edad_menores'] as $key => $edad)
                                            <input type="hidden" name="Edad_menores[{{ $key }}]" value="{{ $edad }}">
                                        @endforeach
                                    @endif

                                    <!-- Campos visibles -->
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control @error('Lugar_Origen') is-invalid @enderror" 
                                                name="Lugar_Origen" placeholder="Ej: Aeropuerto Viru Viru (VVI)" 
                                                value="{{ old('Lugar_Origen') }}" required maxlength="60" oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s()áéíóúÁÉÍÓÚñÑ]/g, '')">
                                            @error('Lugar_Origen')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control @error('Lugar_Destino') is-invalid @enderror" 
                                                name="Lugar_Destino" placeholder="Ej: Hotel Camino Real" 
                                                value="{{ old('Lugar_Destino') }}" required maxlength="60" oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s()áéíóúÁÉÍÓÚñÑ]/g, '')">
                                            @error('Lugar_Destino')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Botón de confirmación DENTRO del formulario -->
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check-circle"></i> Confirmar Reserva
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Fila 4: Precio Total -->
                        <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                            <td><strong>Precio Total</strong></td>
                            <td colspan="2">{{ $respuesta['Precio_Total'] }}</td>
                        </tr>

                        <!-- Fila 5: Fecha Disponible -->
                        <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                            <td><strong>Fecha Disponible</strong></td>
                            <td colspan="2">{{ $respuesta['Fecha_disponible'] }}</td>
                        </tr>

                        <!-- Fila 6: Hora de Servicio -->
                        <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                            <td><strong>Hora Servicio</strong></td>
                            <td colspan="2">{{ $respuesta['hora_servicio'] }}</td>
                        </tr>

                        <!-- Fila 7: Foto del Vehículo -->
                        <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                            <td><strong>Vehículo</strong></td>
                            <td colspan="2">
                                <img src="{{ $respuesta['Foto_tipo_movilidad'] }}" alt="Vehículo" class="img-thumbnail" width="150">
                            </td>
                        </tr>

                        <!-- Fila 8: Pasajeros -->
                        <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                            <td><strong>Pasajeros</strong></td>
                            <td colspan="2">
                                {{ $respuesta['Cantidad_adultos'] }} Adultos / 
                                {{ $respuesta['Cantidad_menores'] }} Menores
                            </td>
                        </tr>

                        <!-- Fila 9: Edades de Menores (opcional) -->
                        @if(isset($respuesta['Edad_menores']))
                        <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-dark text-white' }}">
                            <td><strong>Edades de Menores</strong></td>
                            <td colspan="2">
                                @foreach ($respuesta['Edad_menores'] as $key => $edad)
                                    Menor {{ $key }}: {{ $edad }} años<br>
                                @endforeach
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        @endif
    @endif
</div>
@endsection
