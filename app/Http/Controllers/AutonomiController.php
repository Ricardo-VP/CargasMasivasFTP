<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ClientesModel;
use App\ClientesDireccionModel;
use App\ClientesTelefonoModel;
use App\ClientesEmailModel;
use App\ClientesDatoTipoModel;
use App\DestinatariosModel;
use App\ClientesEmpresaAplicacionModel;
use App\Imports\ExcelImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use File;
use App\Clientes;
use Carbon\Carbon;
use DateTime;
use App\Mail\EnvioCorreos;


use Illuminate\Support\Facades\Storage;

class AutonomiController extends Controller
{  
    public function importarDatos()
    {
        $errores=[];       
        /*$localFile = Storage::disk('ftp_autonomi')->get('autonomi_sftp');
   
       $localFile = Storage::disk('ftp')->get('/autonomi_sftp/Proveedores/Coris/VENTASDIARIAS/ENERO/VENTASDIARIAS.xlsx');

      Storage::disk('local')->put('Autonomi.xlsx', $localFile);*/
      DB::connection('sqlsrv');
    //   dd($users);

      info("*******Inicio de Carga Masiva Autonomi****");
       $array = Excel::toArray(new ExcelImport, 'Autonomi.xlsx');
       try
       {
       foreach ($array[0] as $row) {
        info("Cliente:".json_encode($row));
       
$data=[];
   
           $existe= DB::table('Cliente.Cliente')
               ->where('Identificacion','=',strval( $row["identificacion"]))->get();
               
              

        $formato= date_parse($row["fechainiciovigencia"]);
      $anio=strval($formato['year']);
      $mes=strval($formato['month']);
      $dia=strval($formato['day']);
      $fecha_inicio=$anio.'-'.$mes.'-'.$dia;

      $formato1= date_parse($row["fechainiciovigencia"]);
      $anio=strval($formato1['year']+1);
      $mes=strval($formato1['month']);
      $dia=strval($formato1['day']);
      $fecha_fin=$anio.'-'.$mes.'-'.$dia;

      if( $row["tipoidentificacion"]=="CEDULA")
      {
    
          $tipo_identificacion=2;
      }
      else if( $row["tipoidentificacion"]=="RUC")
      {
          $tipo_identificacion=1;
      }
      else{
          $tipo_identificacion='';
          $data = array('datos'=>$row,'error'=>'TIPO DE IDENTIFICACIÓN NO VÁLIDA');
          array_push($errores,$data);
      }

      $ClienteTipo= DB::table('Cliente.ClienteTipo')
      ->where('Nombre','=', 'TITULAR')->get();
    

        $DatoTipo= DB::table('Configuracion.DatoTipo')
        ->where('Nombre','=', 'DatoAsistenciaAutonomi')->get();

       
        $PlanId= DB::table('Empresa.Plan')
        ->where('Nombre','like', '%'.$row["plancliente"].'%')->get();
       

        $Producto= DB::table('Empresa.Producto')
        ->where('Nombre','=', 'AUTONOMI AUTO CLUB VIAL')->get();
    
           
     

    if(count($PlanId)>0)
    {
     $plan=$PlanId[0]->PlanId; 
      

    } 
    else{
        $data = array('datos'=>$row,'error'=>'EL CAMPO PLAN DETALLADO NO EXISTE NO EXISTE');
        array_push($errores,$data);
      

      info("Cliente: ".json_encode($errores));
      //  Log::info("Error: La sintaxis del campo Plan no es la correcta!. PLAN:".$row["plancliente"]);
    }

    $prefijo=0.0;
    $celular=0.0;

    if(strlen($row["celular"])==6)
    {
        $prefijo="02";
        $celular=$row["celular"];
    }
    else if(strlen($row["celular"])==10)
    
    {
        $prefijo="09";
        $celular=substr($row["celular"], 2); 
    }
   if(strlen($row["celular"])>10)
    {
      
       $prefijo=0.0;
       $celular=0.0;
        $data = array('datos'=>$row,'error'=>'NÚMERO DE CELULAR INCOMPLETO O MUY LARGO');
        array_push($errores,$data);
    
    }
    



    $Dato=[
        "PlanId"=> $plan,
        "FechaInicioVigencia"=> $fecha_inicio,
        "FechaFinVigencia"=> $fecha_fin
    ];

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
               $ClienteDatoTipo=$data[0]->ClienteDatoTipoId;
               else
               $ClienteDatoTipo=0;
           }

    try {
       
     $execute=  DB::raw("exec IngresarDatosB2C :PrimerNombre, :SegundoNombre, :PrimerApellido, :SegundoApellido, :TipoIdentificacion, :Identificacion, :FechaNacimiento, :Telefono, :Celular, :Ciudad, :Direccion, :NumeroCasa, :Sector, :Referencia, :CodigoPostal, :Email, :Plan, :Dato, :DatoTipo, :Producto, :ClienteDatoTipoIdP",[
            ':PrimerNombre' => trim($row["primernombre"]),
            ':SegundoNombre' => $row["segundonombre"],
            ':PrimerApellido' => $row["primerapellido"],
            ':SegundoApellido' => $row["segundoapellido"],
            ':TipoIdentificacion' => $tipo_identificacion,
            ':Identificacion' => strval(TRIM($row["identificacion"])),
            ':FechaNacimiento' =>  $row["fechanacimiento"],
            ':Telefono' => null,
            ':Celular' => $celular,
            ':Ciudad' => 'Quito',
            ':Direccion' => $row["direccion"],
            ':NumeroCasa' => null,
            ':Sector' => null,
            ':Referencia' => null,
            ':CodigoPostal' => null,
            ':Email' => $row["email"],
            ':Plan' => trim($row["plancliente"]),
            ':Dato' => json_encode($Dato),
            ':DatoTipo' => 'DatoAsistenciaAutonomi',
            ':Producto' => 'AUTONOMI AUTO CLUB VIAL',
            ':ClienteDatoTipoIdP' => $ClienteDatoTipo
    
    
        ]);
       info("Query:".json_encode($execute));
    
    } catch (Throwable $e) {
      
       info("Error:"+$e);
        $data = array('datos'=>$row,'error'=>'INFORMACIÓN NO PROCESADA');
        array_push($errores,$data);
     
    }

//RECORREAR TODOS LOS EMAILS DE LOS DESTINATARIO

}
}catch (Throwable $e) {
    info($e);
 

}
$destinatarios = DestinatariosModel::where('ProductoId',$Producto[0]->ProductoId)->pluck('Destinatario');

Mail::to($destinatarios)->send(new EnvioCorreos($errores,'AUTONOMI AUTO CLUB VIAL'));
Log::info("Destinatarios: ".json_encode($destinatarios));
Log::info("******Fin de proceso de carga de datos*****");
}
}

