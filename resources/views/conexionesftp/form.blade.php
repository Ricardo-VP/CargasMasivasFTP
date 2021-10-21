<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('Host') }}
            {{ Form::text('Host', $conexionesftp->Host, ['class' => 'form-control' . ($errors->has('Host') ? ' is-invalid' : ''), 'placeholder' => 'Host']) }}
            {!! $errors->first('Host', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('Puerto') }}
            {{ Form::text('Puerto', $conexionesftp->Puerto, ['class' => 'form-control' . ($errors->has('Puerto') ? ' is-invalid' : ''), 'placeholder' => 'Puerto']) }}
            {!! $errors->first('Puerto', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('Cifrado') }}
            <br>
            {{ Form::select('Cifrado', ['Ftp' => 'Ftp', 'Sftp' => 'Sftp'], $conexionesftp->Cifrado)}}
            {!! $errors->first('Cifrado', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('User') }}
            {{ Form::text('User', $conexionesftp->User, ['class' => 'form-control' . ($errors->has('User') ? ' is-invalid' : ''), 'placeholder' => 'User']) }}
            {!! $errors->first('User', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('Password') }}
            {{ Form::text('Password', $conexionesftp->Password, ['class' => 'form-control' . ($errors->has('Password') ? ' is-invalid' : ''), 'placeholder' => 'Password']) }}
            {!! $errors->first('Password', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('Ruta') }}
            {{ Form::text('Ruta', $conexionesftp->Ruta, ['class' => 'form-control' . ($errors->has('Ruta') ? ' is-invalid' : ''), 'placeholder' => 'Ruta']) }}
            {!! $errors->first('Ruta', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('Activo') }}
            <br>
            {{ Form::radio('Activo', '1', $conexionesftp->Activo == 1 ? true : false) }} Si
            <br>
            {{ Form::radio('Activo', '0', $conexionesftp->Activo == 0 ? true : false) }} No
            {{-- {{ Form::checkbox('Activo', $conexionesftp->Activo, ['class' => 'form-control' . ($errors->has('Activo') ? ' is-invalid' : ''), 'placeholder' => '1']) }} --}}
            {!! $errors->first('Activo', '<div class="invalid-feedback">:message</p>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>