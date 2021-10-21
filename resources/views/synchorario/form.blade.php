{{-- Vaciar los campos de synchorario al cargar --}}
<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('Descripcion') }}
            {{ Form::text('Descripcion', $synchorario->Descripcion, ['class' => 'form-control' . ($errors->has('Descripcion') ? ' is-invalid' : ''), 'placeholder' => 'Horario X']) }}
            {!! $errors->first('Descripcion', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('Hora') }}
            {{ Form::text('Hora', $synchorario->Hora, ['class' => 'form-control' . ($errors->has('Hora') ? ' is-invalid' : ''), 'placeholder' => '12:00:00']) }}
            {!! $errors->first('Hora', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('Activo') }}
            <br>
            {{ Form::radio('Activo', '1', $synchorario->Activo == 1 ? true : false) }} Si
            <br>
            {{ Form::radio('Activo', '0', $synchorario->Activo == 0 ? true : false) }} No
            {{-- {{ Form::checkbox('Activo', $synchorario->Activo, ['class' => 'form-control' . ($errors->has('Activo') ? ' is-invalid' : ''), 'placeholder' => '1']) }} --}}
            {!! $errors->first('Activo', '<div class="invalid-feedback">:message</p>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>