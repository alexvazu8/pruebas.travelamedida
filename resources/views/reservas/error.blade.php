@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Respuestas de Reservas</h1>
    @if(isset($respuestas['error']))
    @php 
    print_r($respuestas);
    @endphp
          {{$respuestas['error']}}
          <a href="{{ url('/carritos/show') }}" class="btn btn-primary btn-lg mx-2">Retornar al Carrito</a>
    @else
        Hay un Error
        @if(isset($mensaje))
         {{$mensaje}}
        @endif
        <a href="{{ url('/carritos/show') }}" class="btn btn-primary btn-lg mx-2">Retornar al Carrito</a>
        
    @endif
</div>
@endsection
