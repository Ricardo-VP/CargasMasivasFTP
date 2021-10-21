@extends('layouts.app')

@section('template_title')
    {{ $synchorario->name ?? 'Show Synchorario' }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Synchorario</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('synchorarios.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Descripcion:</strong>
                            {{ $synchorario->Descripcion }}
                        </div>
                        <div class="form-group">
                            <strong>Hora:</strong>
                            {{ $synchorario->Hora }}
                        </div>
                        <div class="form-group">
                            <strong>Activo:</strong>
                            @if ($synchorario->Activo == 1)
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
