@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h1>403</h1>
    <h3>Acceso denegado</h3>
    <p>No tienes permisos para entrar a esta sección.</p>

    <a href="{{ url('/') }}" class="btn btn-primary">
        Volver al inicio
    </a>
</div>
@endsection