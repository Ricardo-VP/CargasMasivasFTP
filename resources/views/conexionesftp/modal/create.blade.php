<?php
$conexionesftp->Host = null;
$conexionesftp->Puerto = null;
$conexionesftp->Cifrado = null;
$conexionesftp->User = null;
$conexionesftp->Password = null;
$conexionesftp->Ruta = null;
$conexionesftp->Activo = null;
?>
<form action="" method="POST" enctype="multipart/form-data">
    <div class="modal fade text-left" id="ModalCreate" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('Create Conexion FTP')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" arial-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('conexionesftps.store') }}"  role="form" enctype="multipart/form-data">
                        @csrf

                        @include('conexionesftp.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</form>
