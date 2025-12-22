@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-3">{{ __('Respuestas_traslado') }}</h1>
            
            <!-- Nota importante sobre cancelaciones -->
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                    <div>
                        <strong><i class="fas fa-ban me-1"></i> {{ __('Politica_cancelacion') }}</strong> Este servicio 
                        <span class="fw-bold text-danger">{{ __('No_cancelable') }}</span> & 
                        <span class="fw-bold text-danger">{{ __('No_rembolsable') }}</span> {{ __('Advertencia_politica') }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>

    @php
    //print_r($respuestas);
    @endphp
    
    @if(empty($respuestas))
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                <p class="fs-5 text-muted mb-4">{{ __('No_hay_respuesta') }}</p>
                <a href="{{ url('/traslados') }}" class="btn btn-primary btn-lg px-4">
                    <i class="fas fa-arrow-left me-2"></i> Volver a Traslados
                </a>
            </div>
        </div>
    @else
        @if(isset($respuestas['error']))
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-exclamation-circle me-2"></i> Error en la consulta
                </div>
                <div class="card-body">
                    <p class="fs-5">{{ $respuestas['error'] }}</p>
                    
                    @if(isset($respuestas['validation_errors']))
                        <div class="alert alert-danger mt-3">
                            <h6 class="alert-heading"><i class="fas fa-times-circle me-1"></i> Errores de validación:</h6>
                            <ul class="mb-0">
                                @foreach ($respuestas['validation_errors'] as $error)
                                    @foreach ($error as $detalle_error)
                                        <li>{{ $detalle_error }}</li>
                                    @endforeach
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="mt-4">
                        <a href="{{ url('/traslados') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i> Volver a Traslados
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Tarjetas para cada respuesta en lugar de tabla -->
            @foreach($respuestas as $index => $respuesta)
                <div class="card shadow-sm mb-4 border-primary" id="traslado-{{ $index }}">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-car-side me-2"></i>
                            <strong>{{ $respuesta['Nombre_Servicio'] }}</strong>
                        </div>
                        <span class="badge bg-light text-primary fs-6">
                            {{ number_format($respuesta['Precio_Total'], 2) }} USD
                        </span>
                    </div>
                    
                    <div class="card-body">
                        <!-- Detalle del servicio -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-2">
                                    <i class="fas fa-info-circle me-2"></i>Detalle del Servicio
                                </h5>
                                <p class="ps-4">{{ $respuesta['Detalle_servicio'] }}</p>
                            </div>
                        </div>
                        
                        <!-- Información en columnas -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-calendar-day text-primary me-2"></i>
                                    <strong>Fecha Disponible:</strong>
                                </div>
                                <p class="ps-4 mb-0">{{ $respuesta['Fecha_disponible'] }}</p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-clock text-primary me-2"></i>
                                    <strong>Hora de Servicio:</strong>
                                </div>
                                <p class="ps-4 mb-0">{{ $respuesta['hora_servicio'] }}</p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-users text-primary me-2"></i>
                                    <strong>Pasajeros:</strong>
                                </div>
                                <p class="ps-4 mb-0">
                                    {{ $respuesta['Cantidad_adultos'] }} Adultos / 
                                    {{ $respuesta['Cantidad_menores'] }} Menores
                                </p>
                                
                                @if(isset($respuesta['Edad_menores']) && count($respuesta['Edad_menores']) > 0)
                                    <div class="ps-4 mt-2">
                                        <small class="text-muted">
                                            <strong>Edades menores:</strong>
                                            @foreach ($respuesta['Edad_menores'] as $key => $edad)
                                                {{ $edad }}@if(!$loop->last), @endif
                                            @endforeach
                                        </small>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-car text-primary me-2"></i>
                                    <strong>Vehículo:</strong>
                                </div>
                                <div class="ps-4">
                                    <img src="{{ $respuesta['Foto_tipo_movilidad'] }}" 
                                         alt="Vehículo para traslado" 
                                         class="img-thumbnail" 
                                         style="max-width: 200px;">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Formulario para origen/destino -->
                        <div class="border-top pt-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-map-marker-alt me-2"></i>Completar Origen y Destino
                            </h5>
                            
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
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="origen-{{ $index }}" class="form-label">
                                            <i class="fas fa-map-pin me-1"></i> Origen (Hotel o Aeropuerto)
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('Lugar_Origen') is-invalid @enderror" 
                                               id="origen-{{ $index }}"
                                               name="Lugar_Origen" 
                                               placeholder="Ej: Hotel Sol Caribe o Aeropuerto Internacional" 
                                               value="{{ old('Lugar_Origen') }}" 
                                               required 
                                               maxlength="35" 
                                               oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s()áéíóúÁÉÍÓÚñÑ]/g, '')">
                                        <div class="form-text">Máximo 35 caracteres. Solo letras, números y espacios.</div>
                                        @error('Lugar_Origen')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="destino-{{ $index }}" class="form-label">
                                            <i class="fas fa-flag me-1"></i> Destino (Hotel o Aeropuerto)
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('Lugar_Destino') is-invalid @enderror" 
                                               id="destino-{{ $index }}"
                                               name="Lugar_Destino" 
                                               placeholder="Ej: Aeropuerto Internacional o Hotel Playa Dorada" 
                                               value="{{ old('Lugar_Destino') }}" 
                                               required 
                                               maxlength="35" 
                                               oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s()áéíóúÁÉÍÓÚñÑ]/g, '')">
                                        <div class="form-text">Máximo 35 caracteres. Solo letras, números y espacios.</div>
                                        @error('Lugar_Destino')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Recordatorio de no cancelación -->
                                <div class="alert alert-danger d-flex align-items-center mb-4">
                                    <i class="fas fa-ban me-3 fs-4"></i>
                                    <div>
                                        <strong>{{ __('Confirmacion_final') }}</strong> {{ __('Confirmacion_final_texto1') }} <strong class="text-decoration-underline">{{ __('No_cancelable_no_rembolsable') }}</strong> 
                                        {{ __('Bajo_ninguna_circunstancia') }}
                                    </div>
                                </div>
                                
                                <!-- Botón de confirmación -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted">
                                        <small>
                                            <i class="fas fa-check-circle text-success me-1"></i>
                                            {{ __('Verifique_antes_de_confirmar') }}
                                        </small>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-lg px-4">
                                        <i class="fas fa-check-circle me-2"></i> {{ __('Confirm') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Pie de tarjeta con información adicional -->
                    <div class="card-footer bg-light">
                        <div class="row">
                            <div class="col-12">
                                <small class="text-muted">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    <strong>Importante:</strong> El precio mostrado es final. No incluye propinas adicionales.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    @endif
</div>

<!-- Estilos adicionales -->
<style>
    .card {
        transition: transform 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .img-thumbnail {
        border: 2px solid #dee2e6;
        border-radius: 8px;
    }
    
    .form-label {
        font-weight: 500;
        color: #495057;
    }
    
    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        padding: 10px 30px;
    }
    
    .btn-success:hover {
        background: linear-gradient(135deg, #218838 0%, #1ba87e 100%);
        transform: scale(1.05);
    }
</style>
@endsection