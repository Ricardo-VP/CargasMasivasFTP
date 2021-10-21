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
use Log;

use Illuminate\Support\Facades\Storage;




class MascotasBPController extends Controller
{
  
    public function ImportarDatos()
    {
        $errores=[];    
       
      /*  $localFile = Storage::disk('ftp_autonomi')->get('autonomi_sftp');
   
      $localFile = Storage::disk('ftp')->get('/autonomi_sftp/Proveedores/Coris/VENTASDIARIAS/ENERO/VENTASDIARIAS.xlsx');

      Storage::disk('local')->put('Autonomi.xlsx', $localFile);*/
       $array = Excel::toArray(new ExcelImport, 'MascotasBP.xlsx');
      
  try{


       foreach ($array[0] as $row) {

        $existe= DB::table('Cliente.Cliente')
        ->where('Identificacion','=',strval( $row["identificacion"]))->get();
        

        $formato= date_parse($row["iniciovigencia"]);
      $dia=strval($formato['day']);
      $mes=strval($formato['month']);
      $anio=strval($formato['year']);
      $fecha_inicio=$anio.'-'.$mes.'-'.$dia;

      $formato1= date_parse($row["finvigencia"]);
      $dia=strval($formato1['day']+1);
      $mes=strval($formato1['month']);
      $anio=strval($formato1['year']);
      $fecha_fin=$anio.'-'.$mes.'-'.$dia;

      $formato2= date_parse($row["fecha_nacimiento"]);
      $dia=strval($formato1['day']);
      $mes=strval($formato1['month']);
      $anio=strval($formato1['year']);
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
        ->where('Nombre','=', 'MASCOTA PROTEGIDA')->get();
        $CiudadId= DB::table('Catalogo.Ciudad')
        ->where('Nombre','like', '%'.trim($row["ciudad"]).'%')->get();
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
            $ClienteDatoTipo=$data[0]->ClienteDatoTipoId;
            }

        
            $Dato=[
            "TipoMascota"=> "Perro",
            "NombreMascota" => "Perro1",
            "GeneroMascota"=> "",
            "RazaMascota"=> "",
            "FechaNacimientoMascota"=> date('Y-m-d'),
            "FechaInicio"=> $fecha_inicio,
            "FechaFin" => $fecha_fin,
            "PlanId"=> $PlanId[0]->PlanId
        ];

        
 try {
      DB::select(DB::raw("SET NOCOUNT ON ;   exec IngresarDatosB2C :PrimerNombre, :SegundoNombre, :PrimerApellido, :SegundoApellido, :TipoIdentificacion, :Identificacion, :FechaNacimiento, :Telefono, :Celular, :Ciudad, :Direccion, :NumeroCasa, :Sector, :Referencia, :CodigoPostal, :Email, :Plan, :Dato, :DatoTipo, :Producto, :ClienteDatoTipoIdP"),
        [
            ':PrimerNombre' => trim($row["primer_nombre"]),
            ':SegundoNombre' => trim($row["segundo_nombre"]),
            ':PrimerApellido' => trim($row["primer_apellido"]),
            ':SegundoApellido' => trim($row["segundo_apellido"]),
            ':TipoIdentificacion' => $tipo_identificacion,
            ':Identificacion' => strval(trim($row["identificacion"])),
            ':FechaNacimiento' =>  $fecha_nacimiento,
            ':Telefono' => null,
            ':Celular' => null,
            ':Ciudad' => trim($row["ciudad"]),
            ':Direccion' => null,
            ':NumeroCasa' => null,
            ':Sector' => null,
            ':Referencia' => null,
            ':CodigoPostal' => null,
            ':Email' => null,
            ':Plan' => 'PLAN PREMIUM MP',
            ':Dato' => json_encode($Dato),
            ':DatoTipo' => 'DatoAsistenciaMascotasP',
            ':Producto' => 'MASCOTA PROTEGIDA',
            ':ClienteDatoTipoIdP' =>intval($ClienteDatoTipo)
    
    
        ]);
  
   

    
    } catch (Throwable $e) {
        Log::info($e);
        $data = array('datos'=>$row,'error'=>'INFORMACIÓN NO PROCESADA');
        array_push($errores,$data);
     
    }  

    //RECORREAR TODOS LOS EMAILS DE LOS DESTINATARIO
  
  
    }
    }
    catch (Throwable $e) {
        Log::info($e);     
     
    }
    $destinatarios = DestinatariosModel::where('ProductoId',$Producto[0]->ProductoId)->pluck('Destinatario');
  
if(count($destinatarios)>0)
{
   // Mail::to($destinatarios)->send(new EnvioCorreos($errores,'MASCOTA PROTEGIDA'));
}
  
}
     
    }
