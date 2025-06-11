<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="localizador" class="form-label">{{ __('Localizador') }}</label>
            <input type="text" name="Localizador" class="form-control @error('Localizador') is-invalid @enderror" value="{{ old('Localizador', $reserva?->Localizador) }}" id="localizador" placeholder="Localizador">
            {!! $errors->first('Localizador', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="importe__reserva" class="form-label">{{ __('Importe Reserva') }}</label>
            <input type="text" name="Importe_Reserva" class="form-control @error('Importe_Reserva') is-invalid @enderror" value="{{ old('Importe_Reserva', $reserva?->Importe_Reserva) }}" id="importe__reserva" placeholder="Importe Reserva">
            {!! $errors->first('Importe_Reserva', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="nombre__cliente" class="form-label">{{ __('Nombre Cliente') }}</label>
            <input type="text" name="Nombre_Cliente" class="form-control @error('Nombre_Cliente') is-invalid @enderror" value="{{ old('Nombre_Cliente', $reserva?->Nombre_Cliente) }}" id="nombre__cliente" placeholder="Nombre Cliente">
            {!! $errors->first('Nombre_Cliente', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="email_contacto_reserva" class="form-label">{{ __('Email Contacto Reserva') }}</label>
            <input type="text" name="Email_contacto_reserva" class="form-control @error('Email_contacto_reserva') is-invalid @enderror" value="{{ old('Email_contacto_reserva', $reserva?->Email_contacto_reserva) }}" id="email_contacto_reserva" placeholder="Email Contacto Reserva">
            {!! $errors->first('Email_contacto_reserva', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="comentarios" class="form-label">{{ __('Comentarios') }}</label>
            <input type="text" name="Comentarios" class="form-control @error('Comentarios') is-invalid @enderror" value="{{ old('Comentarios', $reserva?->Comentarios) }}" id="comentarios" placeholder="Comentarios">
            {!! $errors->first('Comentarios', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="usuario_id" class="form-label">{{ __('Usuario Id') }}</label>
            <input type="text" name="Usuario_id" class="form-control @error('Usuario_id') is-invalid @enderror" value="{{ old('Usuario_id', $reserva?->Usuario_id) }}" id="usuario_id" placeholder="Usuario Id">
            {!! $errors->first('Usuario_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>