@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Respuestas de disponibilidad Tours</h1>
    @if(isset($respuestas['error']))
    @php 
    print_r($respuestas);
    @endphp
          {{$respuestas['error']}}
          <a href="{{ url('/tours') }}" class="btn btn-primary btn-lg mx-2">Tours</a>
    @else
        Hay un Error
        <a href="{{ url('/tours') }}" class="btn btn-primary btn-lg mx-2">Tours</a>
        
    @endif
</div>
@endsection
