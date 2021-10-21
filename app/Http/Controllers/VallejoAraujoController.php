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


class VallejoAraujoController extends Controller
{
  
    public function ImportarDatos()
    {

      $errores=[];

      /*  $localFile = Storage::disk('ftp_autonomi')->get('autonomi_sftp');
   
      $localFile = Storage::disk('ftp')->get('/autonomi_sftp/Proveedores/Coris/VENTASDIARIAS/ENERO/VENTASDIARIAS.xlsx');

      Storage::disk('local')->put('Autonomi.xlsx', $localFile);*/
       $array = Excel::toArray(new ExcelImport, 'cvaraujo.xlsx');

   try{

         foreach ($array[0] as $row) {
$data=[];

        $existe= DB::table('Cliente.Cliente')
        ->where('Identificacion','=',strval($row["identificacion"]))->get();

          //$fecha_inicio= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fechainicio'])->format('Y-m-d');
          //$fecha_fin= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row["fechafin"])->format('Y-m-d');
          //$fecha_nacimiento= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row["fecha_nacimiento"])->format('Y-m-d');

         $formato= date_parse($row["fechainicio"]);
          $dia=strval($formato['day']);
          $mes=strval($formato['month']);
          $anio=strval($formato['year']);
          $fecha_inicio=$anio.'-'.$mes.'-'.$dia;

         $formato1= date_parse($row["fecha_fin"]);
          $dia=strval($formato1['day']);
          $mes=strval($formato1['month']);
          $anio=strval($formato1['year']);
          $fecha_fin=$anio.'-'.$mes.'-'.$dia;

         $formato2= date_parse($row["fecha_nacimiento"]);
          $dia=strval($formato2['day']);
          $mes=strval($formato2['month']);
          $anio=strval($formato2['year']);
          $fecha_nacimiento=$anio.'-'.$mes.'-'.$dia;

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
          ->where('Nombre','like', '%'.trim($row["plan"]).'%')->get();
        $Producto= DB::table('Empresa.Producto')
          ->where('Nombre','=', 'ECUAPRIMAS ASISTENCIA TALLERES VALLEJO ARAUJO')->get();
//        $CiudadId= DB::table('Catalogo.Ciudad')
//          ->where('Nombre','like', '%'.trim($row["ciudad_domicilio"]).'%')->get();
          $identificacion=trim($row["identificacion"]);

        if(count($PlanId)>0)
    {
       
      

    } 
    else{
        $data = array('datos'=>$row,'error'=>'EL CAMPO PLAN DETALLADO NO EXISTE NO EXISTE');
        array_push($errores,$data);
      

        Log::info("Cliente: ".json_encode($errores));
      //  Log::info("Error: La sintaxis del campo Plan no es la correcta!. PLAN:".$row["plancliente"]);
    }

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

            if(count($data)>0)
            {       $ClienteDatoTipo=$data[0]->ClienteDatoTipoId;
                }
                else{
                     	  $ClienteDatoTipo=0;

                }
            }


        $Dato=[
            "PlanId"=>$PlanId[0]->PlanId,
            "InicioVigencia"=> $fecha_inicio,
            "FinVigencia"=> $fecha_fin
        ];

try {
     DB::select( DB::raw("SET NOCOUNT ON;  exec IngresarDatosB2C :PrimerNombre, :SegundoNombre, :PrimerApellido, :SegundoApellido, :TipoIdentificacion, :Identificacion, :FechaNacimiento, :Telefono, :Celular, :Ciudad, :Direccion, :NumeroCasa, :Sector, :Referencia, :CodigoPostal, :Email, :Plan, :Dato, :DatoTipo, :Producto, :ClienteDatoTipoIdP"),
        [
            ':PrimerNombre' => trim($row["primer_nombre"]),
            ':SegundoNombre' => trim($row["segundo_nombre"]),
            ':PrimerApellido' => trim($row["primer_apellido"]),
            ':SegundoApellido' => trim($row["segundo_apellido"]),
            ':TipoIdentificacion' => $tipo_identificacion,
            ':Identificacion' => strval(trim($row["identificacion"])),
            ':FechaNacimiento' =>  $fecha_nacimiento,
            ':Telefono' => null,
            ':Celular' => trim($row["celular"]),
            ':Ciudad' => null,
            ':Direccion' => trim($row["direccion"]),
            ':NumeroCasa' => null,
            ':Sector' => null,
            ':Referencia' => null,
            ':CodigoPostal' => null,
            ':Email' => trim($row["email"]),
            ':Plan' => trim($row["plan"]),
            ':Dato' => json_encode($Dato),
            ':DatoTipo' => 'DatoEcuaprimasVallejoAraujo',
            ':Producto' => 'ECUAPRIMAS ASISTENCIA TALLERES VALLEJO ARAUJO',
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

        $this->actualizar_Dato($PlanId[0]->PlanId,intval($ClientePadreId[0]->ClienteId),$fecha_inicio,$fecha_fin,intval($Cliente[0]->ClienteId));
        }
        else {
         $Cliente = DB::table('Cliente.Cliente')
            ->select('ClienteId')->where('Identificacion','=',strval(trim($row["identificacion"])))
            ->get();
         $ClientePadreId = DB::table('Cliente.Cliente')
            ->select('ClienteId')->where('Identificacion','=',strval(trim($row["identificacion"])))
            ->get();
           
        $this->actualizar_Dato($PlanId[0]->PlanId,intval($ClientePadreId[0]->ClienteId),$fecha_inicio,$fecha_fin,intval($Cliente[0]->ClienteId));

	
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

public function actualizar_Dato($Plan,$ClientePadreId,$fecha_inicio,$fecha_fin,$Cliente)
{

 $Dato=[
            "PlanId"=>$Plan,
            "ClientePadreId"=> $ClientePadreId,
            "InicioVigencia"=> $fecha_inicio,
            "FinVigencia"=> $fecha_fin
        ];

DB::table('Cliente.ClienteDatoTipo')
->where('ClienteId', $Cliente)
->update(['Dato' => json_encode($Dato)]);


}
     
    }
