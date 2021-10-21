@extends('layouts.app')

@section('template_title')
    {{ $conexionesftp->name ?? 'Show Conexionesftp' }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Conexion FTP</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('conexionesftps.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Host:</strong>
                            {{ $conexionesftp->Host }}
                        </div>
                        <div class="form-group">
                            <strong>Puerto:</strong>
                            {{ $conexionesftp->Puerto }}
                        </div>
                        <div class="form-group">
                            <strong>Cifrado:</strong>
                            {{ $conexionesftp->Cifrado }}
                        </div>
                        <div class="form-group">
                            <strong>User:</strong>
                            {{ $conexionesftp->User }}
                        </div>
                        <div class="form-group">
                            <strong>Password:</strong>
                            {{ $conexionesftp->Password }}
                        </div>
                        <div class="form-group">
                            <strong>Ruta:</strong>
                            {{ $conexionesftp->Ruta }}
                        </div>
                        <div class="form-group">
                            <strong>Activo:</strong>
                            @if ($conexionesftp->Activo == 1)
                                <i class="bi bi-check-circle"></i>
                            @else
                               <i class="bi bi-x-circle"></i>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
