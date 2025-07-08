<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="moneda_origen" class="form-label">{{ __('Moneda Origen') }}</label>
            <input type="text" name="moneda_origen" class="form-control @error('moneda_origen') is-invalid @enderror" value="{{ old('moneda_origen', $tiposCambio?->moneda_origen) }}" id="moneda_origen" placeholder="Moneda Origen">
            {!! $errors->first('moneda_origen', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="moneda_destino" class="form-label">{{ __('Moneda Destino') }}</label>
            <input type="text" name="moneda_destino" class="form-control @error('moneda_destino') is-invalid @enderror" value="{{ old('moneda_destino', $tiposCambio?->moneda_destino) }}" id="moneda_destino" placeholder="Moneda Destino">
            {!! $errors->first('moneda_destino', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="tasa_cambio" class="form-label">{{ __('Tasa Cambio') }}</label>
            <input type="text" name="tasa_cambio" class="form-control @error('tasa_cambio') is-invalid @enderror" value="{{ old('tasa_cambio', $tiposCambio?->tasa_cambio) }}" id="tasa_cambio" placeholder="Tasa Cambio">
            {!! $errors->first('tasa_cambio', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="fecha_validez" class="form-label">{{ __('Fecha Validez') }}</label>
            <input type="text" name="fecha_validez" class="form-control @error('fecha_validez') is-invalid @enderror" value="{{ old('fecha_validez', $tiposCambio?->fecha_validez) }}" id="fecha_validez" placeholder="Fecha Validez">
            {!! $errors->first('fecha_validez', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>