@extends('layouts.app')

@section('content')
<div class="Nosotros-container"> 
    <section class="bg-light py-5 px-4 rounded shadow-sm">
        <div class="container">
            <h2 class="mb-4 text-primary fw-bold">{{ __('text.Titulo_nosotros') }}</h2>
            {!! __('text.Texto_nosotros') !!}
        </div>
    </section>
</div>
@endsection
