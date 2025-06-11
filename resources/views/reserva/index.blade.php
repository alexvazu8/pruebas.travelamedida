@extends('layouts.app')

@section('template_title')
    Reservas
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Reservas') }}
                            </span>
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        
									<th >Localizador</th>
                                    <th >Creacion</th>
									<th >Importe Reserva</th>
									<th >Nombre Cliente</th>
									<th >Email Contacto Reserva</th>
									<th >Comentarios</th>
									<th >Nombre Usuario</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    //print_r($reservas);
                                    @endphp
                                    @foreach ($reservas as $reserva)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $reserva->Localizador }}</td>
                                        <td >{{ $reserva->created_at->format('d/m/Y') }}</td>
										<td >{{ $reserva->Importe_Reserva }}</td>
										<td >{{ $reserva->Nombre_Cliente }} {{ $reserva->Apellido_Cliente }}</td>
										<td >{{ $reserva->Email_contacto_reserva }}</td>
										<td >{{ $reserva->Comentarios }}</td>
										<td >{{ $reserva->user->name }}</td>

                                            <td>                                                
                                                <a class="btn btn-sm btn-primary " href="{{ route('reservas.showReserva', $reserva->Localizador) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $reservas->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
