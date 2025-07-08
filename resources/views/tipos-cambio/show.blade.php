@extends('layouts.app')

@section('template_title')
    {{ $tiposCambio->name ?? __('Show') . " " . __('Tipos Cambio') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Tipos Cambio</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('tipos-cambios.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Moneda Origen:</strong>
                                    {{ $tiposCambio->moneda_origen }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Moneda Destino:</strong>
                                    {{ $tiposCambio->moneda_destino }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Tasa Cambio:</strong>
                                    {{ $tiposCambio->tasa_cambio }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Fecha Validez:</strong>
                                    {{ $tiposCambio->fecha_validez }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
