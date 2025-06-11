@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p>{{ __('text.Login_correcto') }}</p>

                    <div class="d-flex gap-3 mt-4">
                        <a href="{{ route('carritos.show') }}" class="btn btn-primary">
                            🛒 {{ __('Carrito') }}
                        </a>

                        <a href="{{ route('reservas.index') }}" class="btn btn-success">
                            📅 {{ __('Reservas') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
