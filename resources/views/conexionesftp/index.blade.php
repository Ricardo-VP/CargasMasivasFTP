@extends('layouts.app')

@section('template_title')
    Conexiones FTP
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Conexiones FTP') }}
                            </span>

                             <div class="float-right">
                                {{-- <a href="#" data-toggle="modal" data-target="#ModalCreate" class="btn btn-primary btn-sm float-right"  data-placement="left">
                                    {{ __('Create New') }}
                                </a> --}}
                                <a href="{{ route('conexionesftps.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
                                  {{ __('Create New') }}
                                </a>
                              </div>
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>ID</th>
                                        
										<th>Host</th>
										<th>Puerto</th>
										<th>Cifrado</th>
										<th>User</th>
										<th>Password</th>
										<th>Ruta</th>
										<th>Activo</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($conexionesftps as $conexionesftp)
                                        <tr>
                                            <td>{{ $conexionesftp->id }}</td>
                                            
											<td>{{ $conexionesftp->Host }}</td>
											<td>{{ $conexionesftp->Puerto }}</td>
											<td>{{ $conexionesftp->Cifrado }}</td>
											<td>{{ $conexionesftp->User }}</td>
											<td>{{ $conexionesftp->Password }}</td>
											<td>{{ $conexionesftp->Ruta }}</td>
											<td>
                                                @if ($conexionesftp->Activo == 1)
                                                    <i class="bi bi-check-circle"></i>
                                                @else
                                                    <i class="bi bi-x-circle"></i>
                                                @endif
                                            </td>

                                            <td>
                                                <form action="{{ route('conexionesftps.destroy',$conexionesftp->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('conexionesftps.show',$conexionesftp->id) }}"><i class="fa fa-fw fa-eye"></i> Show</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('conexionesftps.edit',$conexionesftp->id) }}"><i class="fa fa-fw fa-edit"></i> Edit</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-fw fa-trash"></i> Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $conexionesftps->links() !!}
            </div>
        </div>
    </div>
    {{-- @include('conexionesftp.modal.create') --}}
@endsection
