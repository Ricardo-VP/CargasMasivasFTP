<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ClientesModel;
use App\ClientesDireccionModel;
use App\ClientesTelefonoModel;
use App\ClientesEmailModel;
use App\DestinatariosModel;
use App\ClientesDatoTipoModel;
use App\ClientesEmpresaAplicacionModel;
use App\Imports\ExcelImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use File;
use App\Clientes;
use Carbon\Carbon;
use App\Mail\EnvioCorreos;
use DateTime;
use Mail;



use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Log;


class SagrarioHijosController extends Controller
{
  
    public function ImportarDatos()
    {

      $errores=[];

      /*  $localFile = Storage::disk('ftp_autonomi')->get('autonomi_sftp');
   
      $localFile = Storage::disk('ftp')->get('/autonomi_sftp/Proveedores/Coris/VENTASDIARIAS/ENERO/VENTASDIARIAS.xlsx');

      Storage::disk('local')->put('Autonomi.xlsx', $localFile);*/
       $array = Excel::toArray(new ExcelImport, 'SagrarioHijos.xlsx');

   try{

         foreach ($array[0] as $row) {
$data=[];

        $existe= DB::table('Cliente.Cliente')
        ->where('Identificacion','=',strval($row["identificacion"]))->get();

          //$fecha_inicio= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['inicio_de_vigencia'])->format('Y-m-d');
          //$fecha_fin= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row["fin_de_vigencia"])->format('Y-m-d');
          //$fecha_nacimiento= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row["fecha_nacimiento"])->format('Y-m-d');

          $formato= date_parse($row["fecha_nacimiento"]);
          $dia=strval($formato['day']);
          $mes=strval($formato['month']);
          $anio=strval($formato['year']);
          $fecha_nacimiento=$anio.'-'.$mes.'-'.$dia;

          $nombres = explode(" ", trim($row["nombre"]));

       if(strlen(trim($row["identificacion"]))<=10)
        {
        
      
            $tipo_identificacion='CEDULA';
        }
        else
        {
            $tipo_identificacion='RUC';
        }

        $ClienteTipo= DB::table('Cliente.ClienteTipo')
        ->where('Nombre','=', 'TITULAR')->get();
        $PlanId= DB::table('Empresa.Plan')
          ->where('Nombre','like', '%MENOR DE EDAD%')->get();
        $Producto= DB::table('Empresa.Producto')
          ->where('Nombre','=', 'ASISTENCIA MEDICA AMBULATORIA')->get();
//        $CiudadId= DB::table('Catalogo.Ciudad')
//          ->where('Nombre','like', '%'.trim($row["ciudad_domicilio"]).'%')->get();
          $identificacion=trim($row["identificacion"]);

          if(count($existe)==0)
     {
     	  $ClienteDatoTipo=0;


        }
        else {
      $data = DB::table('Cliente.Cliente')
            ->join('Cliente.ClienteDatoTipo', 'Cliente.Cliente.ClienteId', '=', 'Cliente.ClienteDatoTipo.ClienteId')
            ->join('Cliente.ClienteEmpresaAplicacion', 'Cliente.ClienteDatoTipo.ClienteDatoTipoId', '=', 'Cliente.ClienteEmpresaAplicacion.ClienteDatoTipoId')
            ->select('Cliente.ClienteDatoTipo.ClienteDatoTipoId')
             ->where('Cliente.Cliente.Identificacion','=', strval($row["identificacion"]),'and','Cliente.ClienteEmpresaAplicacion.ProductoId','=',$Producto[0]->ProductoId)
             ->distinct()
            ->get();
            $ClienteDatoTipo=$data[0]->ClienteDatoTipoId;
            }


        $Dato=[
            "PlanId"=>$PlanId[0]->PlanId
        ];

try {
     DB::select( DB::raw("SET NOCOUNT ON;  exec IngresarDatosB2C :PrimerNombre, :SegundoNombre, :PrimerApellido, :SegundoApellido, :TipoIdentificacion, :Identificacion, :FechaNacimiento, :Telefono, :Celular, :Ciudad, :Direccion, :NumeroCasa, :Sector, :Referencia, :CodigoPostal, :Email, :Plan, :Dato, :DatoTipo, :Producto, :ClienteDatoTipoIdP"),
        [
            ':PrimerNombre' => $nombres[0],
            ':SegundoNombre' => $nombres[1],
            ':PrimerApellido' => $nombres[2],
            ':SegundoApellido' => $nombres[3],
            ':TipoIdentificacion' => $tipo_identificacion,
            ':Identificacion' => strval(trim($row["identificacion"])),
            ':FechaNacimiento' =>  $fecha_nacimiento,
            ':Telefono' => null,
            ':Celular' => null,
            ':Ciudad' => null,
            ':Direccion' => null,
            ':NumeroCasa' => null,
            ':Sector' => null,
            ':Referencia' => null,
            ':CodigoPostal' => null,
            ':Email' => null,
            ':Plan' => 'MENOR DE EDAD',
            ':Dato' => json_encode($Dato),
            ':DatoTipo' => 'DatoAsistenciaMedicaAmbulatoria',
            ':Producto' => 'ASISTENCIA MEDICA AMBULATORIA',
            ':ClienteDatoTipoIdP' =>intval($ClienteDatoTipo)
    
    
        ]);
         $ClientePadreId=0;
        if(trim($row["identificacion"])==trim($row["identificacion"]))
        {
        $Cliente = DB::table('Cliente.Cliente')
            ->select('ClienteId')->where('Identificacion','=',strval(trim($row["identificacion"])))
            ->get();


        $ClientePadreId = DB::table('Cliente.Cliente')
            ->select('ClienteId')->where('Identificacion','=',strval(trim($row["identificacion"])))
            ->get();

        $this->actualizar_Dato($PlanId[0]->PlanId,intval($ClientePadreId[0]->ClienteId),intval($Cliente[0]->ClienteId));
        }
        else {
         $Cliente = DB::table('Cliente.Cliente')
            ->select('ClienteId')->where('Identificacion','=',strval(trim($row["identificacion"])))
            ->get();
         $ClientePadreId = DB::table('Cliente.Cliente')
            ->select('ClienteId')->where('Identificacion','=',strval(trim($row["identificacion"])))
            ->get();
           
        $this->actualizar_Dato($PlanId[0]->PlanId,intval($ClientePadreId[0]->ClienteId),intval($Cliente[0]->ClienteId));

	
}

      
  
    
} catch (Throwable $e) {
        Log::info($e);
        $data = array('datos'=>$row,'error'=>'INFORMACIÓN NO PROCESADA');
        array_push($errores,$data);
     
    }  

    //RECORREAR TODOS LOS EMAILS DE LOS DESTINATARIO
  
  
    }

} catch (Throwable $e) {
        Log::info($e);     
     
    }
    $destinatarios = DestinatariosModel::where('ProductoId',$Producto[0]->ProductoId)->pluck('Destinatario');
  
if(count($destinatarios)>0)
{
   // Mail::to($destinatarios)->send(new EnvioCorreos($errores,'MASCOTA PROTEGIDA'));
}
  
}

public function actualizar_Dato($Plan,$ClientePadreId,$Cliente)
{

 $Dato=[
            "PlanId"=>$Plan,
            "ClientePadreId"=> $ClientePadreId
        ];

DB::table('Cliente.ClienteDatoTipo')
->where('ClienteId', $Cliente)
->update(['Dato' => json_encode($Dato)]);


}
     
    }
