@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Respuestas de disponibilidad Hoteles</h1>
    @if(isset($respuestas['error']))
    @php 
    print_r($respuestas);
    @endphp
          {{$respuestas['error']}}
          <a href="{{ url('/hoteles') }}" class="btn btn-primary btn-lg mx-2">Hoteles</a>
    @else
        Hay un Error
        <a href="{{ url('/hoteles') }}" class="btn btn-primary btn-lg mx-2">Hoteles</a>
        
    @endif
</div>
@endsection
