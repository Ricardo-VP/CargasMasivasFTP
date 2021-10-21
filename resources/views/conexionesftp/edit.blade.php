@extends('layouts.app')

@section('template_title')
    Update Conexionesftp
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                @includeif('partials.errors')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Update Conexion FTP</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('conexionesftps.update', $conexionesftp->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('conexionesftp.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
