@extends('layouts.app')

@section('template_title')
    Synchorario
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Horario Sincronizaciones') }}
                            </span>

                              <div class="float-right">
                                <a href="#" data-toggle="modal" data-target="#ModalCreate" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
										<th>Descripcion</th>
										<th>Hora</th>
										<th>Activo</th>

                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($synchorarios as $synchorario)
                                        <tr>
                                            <td>{{ $synchorario->id }}</td>
                                            
											<td>{{ $synchorario->Descripcion }}</td>
											<td>{{ $synchorario->Hora }}</td>
											<td> 
                                                @if ($synchorario->Activo == 1)
                                                    <i class="bi bi-check-circle"></i>
                                                @else
                                                    <i class="bi bi-x-circle"></i>
                                                @endif
                                            </td>

                                            <td>
                                                <form action="{{ route('synchorarios.destroy',$synchorario->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('synchorarios.show',$synchorario->id) }}"><i class="fa fa-fw fa-eye"></i> Show</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('synchorarios.edit',$synchorario->id) }}"><i class="fa fa-fw fa-edit"></i> Edit</a>
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
                {!! $synchorarios->links() !!}
            </div>
        </div>
    </div>
    @include('synchorario.modal.create')
@endsection
