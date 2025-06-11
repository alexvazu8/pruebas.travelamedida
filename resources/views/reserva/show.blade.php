@extends('layouts.app')

@section('template_title')
    {{ $reserva->name ?? __('Show') . " " . __('Reserva') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Reserva</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('reservas.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Localizador:</strong>
                                    {{ $reserva->Localizador }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Importe Reserva:</strong>
                                    {{ $reserva->Importe_Reserva }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Nombre Cliente:</strong>
                                    {{ $reserva->Nombre_Cliente }} 
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Email Contacto Reserva:</strong>
                                    {{ $reserva->Email_contacto_reserva }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Comentarios:</strong>
                                    {{ $reserva->Comentarios }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Usuario Id:</strong>
                                    {{ $reserva->Usuario_id }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
