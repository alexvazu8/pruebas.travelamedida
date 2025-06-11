@extends('layouts.app')

@section('content')
<div class="container py-5">
    <section class="bg-white p-4 rounded shadow-sm">
        <h2 class="mb-4 text-primary fw-bold"> {!! __('text.Titulo_privacidad') !!}</h2>

        {!! __('text.Texto_privacidad') !!}
    </section>
</div>
@endsection