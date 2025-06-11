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
            <p> <strong>Error no  detectado.</strong> </p>
            <a href="{{ url('/traslados') }}" class="btn btn-primary btn-lg mx-2">Traslados</a>
        @endif
    @endif