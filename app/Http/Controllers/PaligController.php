<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ClientesModel;
use App\ClientesDireccionModel;
use App\ClientesTelefonoModel;
use App\ClientesEmailModel;
use App\ClientesDatoTipoModel;
use App\ClientesEmpresaAplicacionModel;
use App\Imports\ExcelImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use File;
use App\Clientes;
use Carbon\Carbon;
use DateTime;
use App\Mail\EnvioCorreos;
use Mail;
use Log;



use Illuminate\Support\Facades\Storage;




class PaligController extends Controller
{
  
    public function ImportarDatos()
    {
        $errores=[];
       
      /*  $localFile = Storage::disk('ftp_autonomi')->get('autonomi_sftp');
   
       $localFile = Storage::disk('ftp')->get('/autonomi_sftp/Proveedores/Coris/VENTASDIARIAS/ENERO/VENTASDIARIAS.xlsx');

      Storage::disk('local')->put('Autonomi.xlsx', $localFile);*/
    
     // $fecha_actual=date("F");
  
       $array = Excel::toArray(new ExcelImport, 'CORISAMBULATARIAENE.xlsx');
    
       foreach ($array[1] as $row) {
          
         

         

        $existe= DB::table('Cliente.Cliente')
        ->where('Identificacion','=',strval( $row["cedula"]))->get();
 
        $fecha_inicio=  \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_inicio'])->format('Y-m-d');//\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_inicio'])->format('Y-m-d');


        $fecha_fin= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_fin'])->format('Y-m-d'); 
        
        $fecha_nac='2020-01-01'; 
     

        
      
   

 
    
      $ClienteTipo= DB::table('Cliente.ClienteTipo')
      ->where('Nombre','=', 'TITULAR')->get();

        $DatoTipo= DB::table('Configuracion.DatoTipo')
        ->where('Nombre','=', 'DatoAsistenciaPanamericanLife')->get();


        //SACAR BIENE EL PLAN 
        $PlanId= DB::table('Empresa.Plan')
        ->where('Nombre','like', '%PLAN PALIG 2%')->get();

        $Producto= DB::table('Empresa.Producto')
        ->where('Nombre','=', 'ASISTENCIA MEDICA INTERNACIONAL')->get();


    if(count($PlanId)>0)
    {
       
        $Dato=[
            "PlanId"=> strval($PlanId[0]->PlanId),
            "FechaInicioVigencia"=>(string)$fecha_inicio,
            "FechaFinVigencia"=>(string) $fecha_fin,
            "EmpresaAdquirente"=>$row["cli_emp"],
            "ClienteCodigoEmpresa"=>$row["cli_cod_fam"],
            "CodigoDepartamento"=>$row["cod_dep"],
            "CodigoTipoCliente"=>$row["cli_tip_per_cod"],
            "ClienteDepartamento"=>$row["cli_dep"],
            "CodigoPlan"=>$row["plan_code"],
            "Clase"=>$row["clase"]
        ];
  
  

    } 
    else{
        $plan = array('datos'=>$row,'error'=>'EL PLAN NO EXISTE');
        array_push($errores,$plan);
      

        Log::info("Cliente: ".json_encode($errores));
      //  Log::info("Error: La sintaxis del campo Plan no es la correcta!. PLAN:".$row["plancliente"]);
    }


      
       
     
 

     
     if(count($existe)==0)
     {      
        

        if( strlen($row["cedula"])<=10)
        {
      
            $tipo_identificacion=2;
        }
        else
        {
            $tipo_identificacion=1;
        }
 
      
        $nombres = explode(" ", trim($row["cli_nom"])); 

//GUARDAR EL CLIENTE

        $cliente = new ClientesModel;
     
        $cliente->IdentificacionTipoId= $tipo_identificacion;
        $cliente->ClienteTipoId= $ClienteTipo[0]->ClienteTipoId;
        $cliente->SexoId=null;
        $cliente->CiudadId= null;
        $cliente->Identificacion= strval(TRIM($row["cedula"]));

        if(!empty($nombres[2]))
        {  
             $cliente->PrimerNombre= $nombres[2];   
        }
        if(!empty($nombres[3]))
        {
            
            if(!empty($nombres[4]))
        {
            
            $cliente->SegundoNombre= $nombres[3].' '.$nombres[4];
        }
        $cliente->SegundoNombre= $nombres[3];
        }
        if(!empty($nombres[0]))
        {
            $cliente->PrimerApellido= $nombres[0];
       
        }
        if(!empty($nombres[1]))
        {
            $cliente->SegundoApellido= $nombres[1];
       
        }  
     
        $cliente->NombreCompleto= $row["cli_nom"];
        $cliente->FechaNacimiento= (string)$fecha_nac;
        $cliente->Observacion= null;
        $cliente->EsVip= 1;
        $cliente->EsHabilitado= 1;
        $cliente->EsEliminado= 0;
        $cliente->FechaCreacion= date("Y-m-d H:i:s");
        $cliente->UsuarioCreacionId= 1720442;
        $cliente->FechaModificacion= date("Y-m-d H:i:s");
        $cliente->UsuarioModificacionId= 1720442;
     
    
        try {
            $cliente->save();
        } catch (Throwable $e) {
            Log::info($e);
    
         
        }
 
     
       
       $ClienteId=$cliente->ClienteId;

///GUARDAR LA DIRECCION
//$order = ClientesDireccionModel::find(\DB::table('Cliente.ClienteDireccion')->max('id'));
       // $id = DB::table('Cliente.ClienteDireccion')->max('ClienteDireccionId');

  /*      $clienteDireccion = new ClientesDireccionModel;
       
        //$clienteDireccion->ClienteDireccionId=$id+1;
        $clienteDireccion->ClienteId=$ClienteId;
        $clienteDireccion->Direccion=$row["direccion"];
        $clienteDireccion->Numero=null;
        $clienteDireccion->Sector=null;
        $clienteDireccion->Referencia=null;
        $clienteDireccion->CodigoPostal=null;
        $clienteDireccion->Posicion=null;
        $clienteDireccion->EsHabilitado= 1;
        $clienteDireccion->EsEliminado= 0;
        $clienteDireccion->FechaCreacion= date("Y-m-d H:i:s");
        $clienteDireccion->UsuarioCreacionId= 1720442;
        $clienteDireccion->FechaModificacion= date("Y-m-d H:i:s");
        $clienteDireccion->UsuarioModificacionId= 1720442;
        try {
            $clienteDireccion->save();
        } catch (Throwable $e) {
            Log::info($e);
    
         
        }*/
 
       
/*
if(strlen($row["celular"])<10)
{
    $prefijo="02";
    $celular=$row["celular"];
}
else{
    $prefijo="09";
    $celular=substr($row["celular"], 2); 
    

}

        //GUARDAR TELEFONO
        $ClienteTelefonoId = DB::table('Cliente.ClienteTelefono')->max('ClienteTelefonoId');
        $clienteTelefono = new ClientesTelefonoModel;
        $clienteTelefono->ClienteTelefonoId=$ClienteTelefonoId+1;
        $clienteTelefono->ClienteId=$ClienteId;
        $clienteTelefono->Operadora=null;
        $clienteTelefono->Prefijo=$prefijo;
        $clienteTelefono->Numero=$celular;
        $clienteTelefono->Extension=null;
        $clienteTelefono->EsFavorito=1;
        $clienteTelefono->EsHabilitado= 1;
        $clienteTelefono->EsEliminado= 0;
        $clienteTelefono->FechaCreacion= date("Y-m-d H:i:s");
        $clienteTelefono->UsuarioCreacionId= 1720442;
        $clienteTelefono->FechaModificacion= date("Y-m-d H:i:s");
        $clienteTelefono->UsuarioModificacionId= 1720442;
     
        try {
            $clienteTelefono->save();
        } catch (Throwable $e) {
            Log::info($e);
    
         
        }*/
        

        //GUARDAR MAIL

  /*      $ClienteEmailId = DB::table('Cliente.ClienteEmail')->max('ClienteEmailId');

        $clienteEmail= new ClientesEmailModel;
        $clienteEmail->ClienteEmailId=$ClienteEmailId+1;
        $clienteEmail->ClienteId=$ClienteId;
        $clienteEmail->Email=$row["email"];   
        $clienteEmail->EsFavorito=1;
        $clienteEmail->EsHabilitado= 1;
        $clienteEmail->EsEliminado= 0;
        $clienteEmail->FechaCreacion= date("Y-m-d H:i:s");
        $clienteEmail->UsuarioCreacionId= 1720442;
        $clienteEmail->FechaModificacion= date("Y-m-d H:i:s");
        $clienteEmail->UsuarioModificacionId= 1720442;
     
        try {
            $clienteEmail->save();
        } catch (Throwable $e) {
            Log::info($e);
    
         
        }*/
        


        //GUARDAR CLIENTE DATO TIPO
    
      $ClienteDatoTipo = DB::table('Cliente.ClienteDatoTipo')->max('ClienteDatoTipoId');

        $clienteDatoTipo= new ClientesDatoTipoModel;
        $clienteDatoTipo->ClienteDatoTipoId=$ClienteDatoTipo+1;
        $clienteDatoTipo->DatoTipoId=$DatoTipo[0]->DatoTipoId;
        $clienteDatoTipo->ClienteId=$ClienteId;
        $clienteDatoTipo->Dato=json_encode($Dato);   
        $clienteDatoTipo->EsHabilitado= 1;
        $clienteDatoTipo->EsEliminado= 0;
        $clienteDatoTipo->FechaCreacion= date("Y-m-d H:i:s");
        $clienteDatoTipo->UsuarioCreacionId= 1720442;
        $clienteDatoTipo->FechaModificacion= date("Y-m-d H:i:s");
        $clienteDatoTipo->UsuarioModificacionId= 1720442;
     
        try {
            $clienteDatoTipo->save();
        } catch (Throwable $e) {
            Log::info($e);
    
         
        }
       

        $ClienteDatoTipoId=$clienteDatoTipo->ClienteDatoTipoId;

        $ClienteEmpresaAplicacionId = DB::table('Cliente.ClienteEmpresaAplicacion')->max('ClienteEmpresaAplicacionId');
      
      
        $clienteEmpresaAplicacion= new ClientesEmpresaAplicacionModel;
        $clienteEmpresaAplicacion->ClienteEmpresaAplicacionId=$ClienteEmpresaAplicacionId+1;
        $clienteEmpresaAplicacion->ClienteDatoTipoId=$ClienteDatoTipoId;
        $clienteEmpresaAplicacion->ProductoId=$Producto[0]->ProductoId; 
        $clienteEmpresaAplicacion->EsHabilitado= 1;
        $clienteEmpresaAplicacion->EsEliminado= 0;
        $clienteEmpresaAplicacion->FechaCreacion= date("Y-m-d H:i:s");
        $clienteEmpresaAplicacion->UsuarioCreacionId= 1720442;
        $clienteEmpresaAplicacion->FechaModificacion= date("Y-m-d H:i:s");
        $clienteEmpresaAplicacion->UsuarioModificacionId= 1720442;
        try {
            $clienteEmpresaAplicacion->save();
        } catch (Throwable $e) {
            Log::info($e);
    
         
        }
        


    }
 
    }

       
    
   // PALIG CORP

   /*  $localFile = Storage::disk('ftp_autonomi')->get('autonomi_sftp');
   
       $localFile = Storage::disk('ftp')->get('/autonomi_sftp/Proveedores/Coris/VENTASDIARIAS/ENERO/VENTASDIARIAS.xlsx');

      Storage::disk('local')->put('Autonomi.xlsx', $localFile);*/
    
     // $fecha_actual=date("F");
  
     $array = Excel::toArray(new ExcelImport, 'CORISCORPENE.xlsx');
    
     foreach ($array[0] as $row) {
        
       

       

      $existe= DB::table('Cliente.Cliente')
      ->where('Identificacion','=',strval( $row["cli_ci"]))->get();

      $fecha_inicio=  \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_inicio'])->format('Y-m-d');//\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_inicio'])->format('Y-m-d');


      $fecha_fin= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_fin'])->format('Y-m-d'); 
      
      $fecha_nac= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['cli_fec_nac'])->format('Y-m-d'); 
   

      
    
 


  
    $ClienteTipo= DB::table('Cliente.ClienteTipo')
    ->where('Nombre','=', 'TITULAR')->get();

      $DatoTipo= DB::table('Configuracion.DatoTipo')
      ->where('Nombre','=', 'DatoAsistenciaPanamericanLife')->get();


      //SACAR BIENE EL PLAN 
      $PlanId= DB::table('Empresa.Plan')
      ->where('Nombre','like', 'PLAN PALIG '.$row['plan_code'])->get();

      $Producto= DB::table('Empresa.Producto')
      ->where('Nombre','=', 'ASISTENCIA MEDICA INTERNACIONAL')->get();


  if(count($PlanId)>0)
  {
     
      $Dato=[
          "PlanId"=> strval($PlanId[0]->PlanId),
          "FechaInicioVigencia"=>(string)$fecha_inicio,
          "FechaFinVigencia"=>(string) $fecha_fin,
          "EmpresaAdquirente"=>$row["cli_emp"],
          "ClienteCodigoEmpresa"=>$row["cli_cod_fam"],
          "CodigoDepartamento"=>$row["cod_dep"],
          "CodigoTipoCliente"=>$row["cli_tip_per_cod"],
          "ClienteDepartamento"=>$row["cli_dep"],
          "CodigoPlan"=>$row["plan_code"],
          "Clase"=>$row["clase"],
          "MontoPlan"=>$row["monto_plan_1"]
      ];



  } 
  else{
      $plan = array('datos'=>$row,'error'=>'EL PLAN NO EXISTE');
      array_push($errores,$plan);
    

      Log::info("Cliente: ".json_encode($errores));
    //  Log::info("Error: La sintaxis del campo Plan no es la correcta!. PLAN:".$row["plancliente"]);
  }


    
     
   


   
   if(count($existe)==0)
   {      
      

      if( strlen($row["cedula"])<=10)
      {
    
          $tipo_identificacion=2;
      }
      else
      {
          $tipo_identificacion=1;
      }

    
      $nombres = explode(" ", trim($row["cli_nom"])); 

//GUARDAR EL CLIENTE

      $cliente = new ClientesModel;
   
      $cliente->IdentificacionTipoId= $tipo_identificacion;
      $cliente->ClienteTipoId= $ClienteTipo[0]->ClienteTipoId;
      $cliente->SexoId=null;
      $cliente->CiudadId= null;
      $cliente->Identificacion= strval(TRIM($row["cedula"]));

      if(!empty($nombres[2]))
      {  
           $cliente->PrimerNombre= $nombres[2];   
      }
      if(!empty($nombres[3]))
      {
          
          if(!empty($nombres[4]))
      {
          
          $cliente->SegundoNombre= $nombres[3].' '.$nombres[4];
      }
      $cliente->SegundoNombre= $nombres[3];
      }
      if(!empty($nombres[0]))
      {
          $cliente->PrimerApellido= $nombres[0];
     
      }
      if(!empty($nombres[1]))
      {
          $cliente->SegundoApellido= $nombres[1];
     
      }  
   
      $cliente->NombreCompleto= $row["cli_nom"];
      $cliente->FechaNacimiento= (string)$fecha_nac;
      $cliente->Observacion= null;
      $cliente->EsVip= 1;
      $cliente->EsHabilitado= 1;
      $cliente->EsEliminado= 0;
      $cliente->FechaCreacion= date("Y-m-d H:i:s");
      $cliente->UsuarioCreacionId= 1720442;
      $cliente->FechaModificacion= date("Y-m-d H:i:s");
      $cliente->UsuarioModificacionId= 1720442;
   
  
      try {
          $cliente->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }

   
     
     $ClienteId=$cliente->ClienteId;

///GUARDAR LA DIRECCION
//$order = ClientesDireccionModel::find(\DB::table('Cliente.ClienteDireccion')->max('id'));
     // $id = DB::table('Cliente.ClienteDireccion')->max('ClienteDireccionId');

/*      $clienteDireccion = new ClientesDireccionModel;
     
      //$clienteDireccion->ClienteDireccionId=$id+1;
      $clienteDireccion->ClienteId=$ClienteId;
      $clienteDireccion->Direccion=$row["direccion"];
      $clienteDireccion->Numero=null;
      $clienteDireccion->Sector=null;
      $clienteDireccion->Referencia=null;
      $clienteDireccion->CodigoPostal=null;
      $clienteDireccion->Posicion=null;
      $clienteDireccion->EsHabilitado= 1;
      $clienteDireccion->EsEliminado= 0;
      $clienteDireccion->FechaCreacion= date("Y-m-d H:i:s");
      $clienteDireccion->UsuarioCreacionId= 1720442;
      $clienteDireccion->FechaModificacion= date("Y-m-d H:i:s");
      $clienteDireccion->UsuarioModificacionId= 1720442;
      try {
          $clienteDireccion->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }*/

     
/*
if(strlen($row["celular"])<10)
{
  $prefijo="02";
  $celular=$row["celular"];
}
else{
  $prefijo="09";
  $celular=substr($row["celular"], 2); 
  

}

      //GUARDAR TELEFONO
      $ClienteTelefonoId = DB::table('Cliente.ClienteTelefono')->max('ClienteTelefonoId');
      $clienteTelefono = new ClientesTelefonoModel;
      $clienteTelefono->ClienteTelefonoId=$ClienteTelefonoId+1;
      $clienteTelefono->ClienteId=$ClienteId;
      $clienteTelefono->Operadora=null;
      $clienteTelefono->Prefijo=$prefijo;
      $clienteTelefono->Numero=$celular;
      $clienteTelefono->Extension=null;
      $clienteTelefono->EsFavorito=1;
      $clienteTelefono->EsHabilitado= 1;
      $clienteTelefono->EsEliminado= 0;
      $clienteTelefono->FechaCreacion= date("Y-m-d H:i:s");
      $clienteTelefono->UsuarioCreacionId= 1720442;
      $clienteTelefono->FechaModificacion= date("Y-m-d H:i:s");
      $clienteTelefono->UsuarioModificacionId= 1720442;
   
      try {
          $clienteTelefono->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }*/
      

      //GUARDAR MAIL

/*      $ClienteEmailId = DB::table('Cliente.ClienteEmail')->max('ClienteEmailId');

      $clienteEmail= new ClientesEmailModel;
      $clienteEmail->ClienteEmailId=$ClienteEmailId+1;
      $clienteEmail->ClienteId=$ClienteId;
      $clienteEmail->Email=$row["email"];   
      $clienteEmail->EsFavorito=1;
      $clienteEmail->EsHabilitado= 1;
      $clienteEmail->EsEliminado= 0;
      $clienteEmail->FechaCreacion= date("Y-m-d H:i:s");
      $clienteEmail->UsuarioCreacionId= 1720442;
      $clienteEmail->FechaModificacion= date("Y-m-d H:i:s");
      $clienteEmail->UsuarioModificacionId= 1720442;
   
      try {
          $clienteEmail->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }*/
      


      //GUARDAR CLIENTE DATO TIPO
  
    $ClienteDatoTipo = DB::table('Cliente.ClienteDatoTipo')->max('ClienteDatoTipoId');

      $clienteDatoTipo= new ClientesDatoTipoModel;
      $clienteDatoTipo->ClienteDatoTipoId=$ClienteDatoTipo+1;
      $clienteDatoTipo->DatoTipoId=$DatoTipo[0]->DatoTipoId;
      $clienteDatoTipo->ClienteId=$ClienteId;
      $clienteDatoTipo->Dato=json_encode($Dato);   
      $clienteDatoTipo->EsHabilitado= 1;
      $clienteDatoTipo->EsEliminado= 0;
      $clienteDatoTipo->FechaCreacion= date("Y-m-d H:i:s");
      $clienteDatoTipo->UsuarioCreacionId= 1720442;
      $clienteDatoTipo->FechaModificacion= date("Y-m-d H:i:s");
      $clienteDatoTipo->UsuarioModificacionId= 1720442;
   
      try {
          $clienteDatoTipo->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }
     

      $ClienteDatoTipoId=$clienteDatoTipo->ClienteDatoTipoId;

      $ClienteEmpresaAplicacionId = DB::table('Cliente.ClienteEmpresaAplicacion')->max('ClienteEmpresaAplicacionId');
    
    
      $clienteEmpresaAplicacion= new ClientesEmpresaAplicacionModel;
      $clienteEmpresaAplicacion->ClienteEmpresaAplicacionId=$ClienteEmpresaAplicacionId+1;
      $clienteEmpresaAplicacion->ClienteDatoTipoId=$ClienteDatoTipoId;
      $clienteEmpresaAplicacion->ProductoId=$Producto[0]->ProductoId; 
      $clienteEmpresaAplicacion->EsHabilitado= 1;
      $clienteEmpresaAplicacion->EsEliminado= 0;
      $clienteEmpresaAplicacion->FechaCreacion= date("Y-m-d H:i:s");
      $clienteEmpresaAplicacion->UsuarioCreacionId= 1720442;
      $clienteEmpresaAplicacion->FechaModificacion= date("Y-m-d H:i:s");
      $clienteEmpresaAplicacion->UsuarioModificacionId= 1720442;
      try {
          $clienteEmpresaAplicacion->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }
      


  }

  }



  
   // PALIG HT

   /*  $localFile = Storage::disk('ftp_autonomi')->get('autonomi_sftp');
   
       $localFile = Storage::disk('ftp')->get('/autonomi_sftp/Proveedores/Coris/VENTASDIARIAS/ENERO/VENTASDIARIAS.xlsx');

      Storage::disk('local')->put('Autonomi.xlsx', $localFile);*/
    
     // $fecha_actual=date("F");
  
     $array = Excel::toArray(new ExcelImport, 'CORISHTENE.xlsx');
    
     foreach ($array[0] as $row) {
        
       

       

      $existe= DB::table('Cliente.Cliente')
      ->where('Identificacion','=',strval( $row["cli_ci"]))->get();

      $fecha_inicio=  \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_inicio'])->format('Y-m-d');//\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_inicio'])->format('Y-m-d');


      $fecha_fin= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_fin'])->format('Y-m-d'); 
      
      $fecha_nac= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['cli_fec_nac'])->format('Y-m-d'); 
   

      
    
 


  
    $ClienteTipo= DB::table('Cliente.ClienteTipo')
    ->where('Nombre','=', 'TITULAR')->get();

      $DatoTipo= DB::table('Configuracion.DatoTipo')
      ->where('Nombre','=', 'DatoAsistenciaPanamericanLife')->get();


      //SACAR BIENE EL PLAN 
      $PlanId= DB::table('Empresa.Plan')
      ->where('Nombre','like', 'PLAN PALIG '.$row['plan_code'])->get();

      $Producto= DB::table('Empresa.Producto')
      ->where('Nombre','=', 'ASISTENCIA MEDICA INTERNACIONAL')->get();


  if(count($PlanId)>0)
  {
     
      $Dato=[
          "PlanId"=> strval($PlanId[0]->PlanId),
          "FechaInicioVigencia"=>(string)$fecha_inicio,
          "FechaFinVigencia"=>(string) $fecha_fin,
          "EmpresaAdquirente"=>$row["cli_emp"],
          "ClienteCodigoEmpresa"=>$row["cli_cod_fam"],
          "CodigoDepartamento"=>$row["cod_dep"],
          "CodigoTipoCliente"=>$row["cli_tip_per_cod"],
          "ClienteDepartamento"=>$row["cli_dep"],
          "CodigoPlan"=>$row["plan_code"],
          "Clase"=>$row["clase"],
          "MontoPlan"=>$row["monto_plan_1"]
      ];



  } 
  else{
      $plan = array('datos'=>$row,'error'=>'EL PLAN NO EXISTE');
      array_push($errores,$plan);
    

      Log::info("Cliente: ".json_encode($errores));
    //  Log::info("Error: La sintaxis del campo Plan no es la correcta!. PLAN:".$row["plancliente"]);
  }


    
     
   


   
   if(count($existe)==0)
   {      
      

      if( strlen($row["cedula"])<=10)
      {
    
          $tipo_identificacion=2;
      }
      else
      {
          $tipo_identificacion=1;
      }

    
      $nombres = explode(" ", trim($row["cli_nom"])); 

//GUARDAR EL CLIENTE

      $cliente = new ClientesModel;
   
      $cliente->IdentificacionTipoId= $tipo_identificacion;
      $cliente->ClienteTipoId= $ClienteTipo[0]->ClienteTipoId;
      $cliente->SexoId=null;
      $cliente->CiudadId= null;
      $cliente->Identificacion= strval(TRIM($row["cedula"]));

      if(!empty($nombres[2]))
      {  
           $cliente->PrimerNombre= $nombres[2];   
      }
      if(!empty($nombres[3]))
      {
          
          if(!empty($nombres[4]))
      {
          
          $cliente->SegundoNombre= $nombres[3].' '.$nombres[4];
      }
      $cliente->SegundoNombre= $nombres[3];
      }
      if(!empty($nombres[0]))
      {
          $cliente->PrimerApellido= $nombres[0];
     
      }
      if(!empty($nombres[1]))
      {
          $cliente->SegundoApellido= $nombres[1];
     
      }  
   
      $cliente->NombreCompleto= $row["cli_nom"];
      $cliente->FechaNacimiento= (string)$fecha_nac;
      $cliente->Observacion= null;
      $cliente->EsVip= 1;
      $cliente->EsHabilitado= 1;
      $cliente->EsEliminado= 0;
      $cliente->FechaCreacion= date("Y-m-d H:i:s");
      $cliente->UsuarioCreacionId= 1720442;
      $cliente->FechaModificacion= date("Y-m-d H:i:s");
      $cliente->UsuarioModificacionId= 1720442;
   
  
      try {
          $cliente->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }

   
     
     $ClienteId=$cliente->ClienteId;

///GUARDAR LA DIRECCION
//$order = ClientesDireccionModel::find(\DB::table('Cliente.ClienteDireccion')->max('id'));
     // $id = DB::table('Cliente.ClienteDireccion')->max('ClienteDireccionId');

/*      $clienteDireccion = new ClientesDireccionModel;
     
      //$clienteDireccion->ClienteDireccionId=$id+1;
      $clienteDireccion->ClienteId=$ClienteId;
      $clienteDireccion->Direccion=$row["direccion"];
      $clienteDireccion->Numero=null;
      $clienteDireccion->Sector=null;
      $clienteDireccion->Referencia=null;
      $clienteDireccion->CodigoPostal=null;
      $clienteDireccion->Posicion=null;
      $clienteDireccion->EsHabilitado= 1;
      $clienteDireccion->EsEliminado= 0;
      $clienteDireccion->FechaCreacion= date("Y-m-d H:i:s");
      $clienteDireccion->UsuarioCreacionId= 1720442;
      $clienteDireccion->FechaModificacion= date("Y-m-d H:i:s");
      $clienteDireccion->UsuarioModificacionId= 1720442;
      try {
          $clienteDireccion->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }*/

     
/*
if(strlen($row["celular"])<10)
{
  $prefijo="02";
  $celular=$row["celular"];
}
else{
  $prefijo="09";
  $celular=substr($row["celular"], 2); 
  

}

      //GUARDAR TELEFONO
      $ClienteTelefonoId = DB::table('Cliente.ClienteTelefono')->max('ClienteTelefonoId');
      $clienteTelefono = new ClientesTelefonoModel;
      $clienteTelefono->ClienteTelefonoId=$ClienteTelefonoId+1;
      $clienteTelefono->ClienteId=$ClienteId;
      $clienteTelefono->Operadora=null;
      $clienteTelefono->Prefijo=$prefijo;
      $clienteTelefono->Numero=$celular;
      $clienteTelefono->Extension=null;
      $clienteTelefono->EsFavorito=1;
      $clienteTelefono->EsHabilitado= 1;
      $clienteTelefono->EsEliminado= 0;
      $clienteTelefono->FechaCreacion= date("Y-m-d H:i:s");
      $clienteTelefono->UsuarioCreacionId= 1720442;
      $clienteTelefono->FechaModificacion= date("Y-m-d H:i:s");
      $clienteTelefono->UsuarioModificacionId= 1720442;
   
      try {
          $clienteTelefono->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }*/
      

      //GUARDAR MAIL

/*      $ClienteEmailId = DB::table('Cliente.ClienteEmail')->max('ClienteEmailId');

      $clienteEmail= new ClientesEmailModel;
      $clienteEmail->ClienteEmailId=$ClienteEmailId+1;
      $clienteEmail->ClienteId=$ClienteId;
      $clienteEmail->Email=$row["email"];   
      $clienteEmail->EsFavorito=1;
      $clienteEmail->EsHabilitado= 1;
      $clienteEmail->EsEliminado= 0;
      $clienteEmail->FechaCreacion= date("Y-m-d H:i:s");
      $clienteEmail->UsuarioCreacionId= 1720442;
      $clienteEmail->FechaModificacion= date("Y-m-d H:i:s");
      $clienteEmail->UsuarioModificacionId= 1720442;
   
      try {
          $clienteEmail->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }*/
      


      //GUARDAR CLIENTE DATO TIPO
  
    $ClienteDatoTipo = DB::table('Cliente.ClienteDatoTipo')->max('ClienteDatoTipoId');

      $clienteDatoTipo= new ClientesDatoTipoModel;
      $clienteDatoTipo->ClienteDatoTipoId=$ClienteDatoTipo+1;
      $clienteDatoTipo->DatoTipoId=$DatoTipo[0]->DatoTipoId;
      $clienteDatoTipo->ClienteId=$ClienteId;
      $clienteDatoTipo->Dato=json_encode($Dato);   
      $clienteDatoTipo->EsHabilitado= 1;
      $clienteDatoTipo->EsEliminado= 0;
      $clienteDatoTipo->FechaCreacion= date("Y-m-d H:i:s");
      $clienteDatoTipo->UsuarioCreacionId= 1720442;
      $clienteDatoTipo->FechaModificacion= date("Y-m-d H:i:s");
      $clienteDatoTipo->UsuarioModificacionId= 1720442;
   
      try {
          $clienteDatoTipo->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }
     

      $ClienteDatoTipoId=$clienteDatoTipo->ClienteDatoTipoId;

      $ClienteEmpresaAplicacionId = DB::table('Cliente.ClienteEmpresaAplicacion')->max('ClienteEmpresaAplicacionId');
    
    
      $clienteEmpresaAplicacion= new ClientesEmpresaAplicacionModel;
      $clienteEmpresaAplicacion->ClienteEmpresaAplicacionId=$ClienteEmpresaAplicacionId+1;
      $clienteEmpresaAplicacion->ClienteDatoTipoId=$ClienteDatoTipoId;
      $clienteEmpresaAplicacion->ProductoId=$Producto[0]->ProductoId; 
      $clienteEmpresaAplicacion->EsHabilitado= 1;
      $clienteEmpresaAplicacion->EsEliminado= 0;
      $clienteEmpresaAplicacion->FechaCreacion= date("Y-m-d H:i:s");
      $clienteEmpresaAplicacion->UsuarioCreacionId= 1720442;
      $clienteEmpresaAplicacion->FechaModificacion= date("Y-m-d H:i:s");
      $clienteEmpresaAplicacion->UsuarioModificacionId= 1720442;
      try {
          $clienteEmpresaAplicacion->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }
      


  }

  }



  // PALIG MMA CORIS

   /*  $localFile = Storage::disk('ftp_autonomi')->get('autonomi_sftp');
   
       $localFile = Storage::disk('ftp')->get('/autonomi_sftp/Proveedores/Coris/VENTASDIARIAS/ENERO/VENTASDIARIAS.xlsx');

      Storage::disk('local')->put('Autonomi.xlsx', $localFile);*/
    
     // $fecha_actual=date("F");
  
     $array = Excel::toArray(new ExcelImport, 'CORISMMAENE.xlsx');
    
     foreach ($array[0] as $row) {
        
       

       

      $existe= DB::table('Cliente.Cliente')
      ->where('Identificacion','=',strval( $row["cli_ci"]))->get();

      $fecha_inicio=  \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_inicio'])->format('Y-m-d');//\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_inicio'])->format('Y-m-d');


      $fecha_fin= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_fin'])->format('Y-m-d'); 
      
      $fecha_nac= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['cli_fec_nac'])->format('Y-m-d'); 
   

      
    
 


  
    $ClienteTipo= DB::table('Cliente.ClienteTipo')
    ->where('Nombre','=', 'TITULAR')->get();

      $DatoTipo= DB::table('Configuracion.DatoTipo')
      ->where('Nombre','=', 'DatoAsistenciaPanamericanLife')->get();


      //SACAR BIENE EL PLAN 
      $PlanId= DB::table('Empresa.Plan')
      ->where('Nombre','like', 'PLAN PALIG '.$row['plan_code'])->get();

      $Producto= DB::table('Empresa.Producto')
      ->where('Nombre','=', 'ASISTENCIA MEDICA INTERNACIONAL')->get();


  if(count($PlanId)>0)
  {
     
      $Dato=[
          "PlanId"=> strval($PlanId[0]->PlanId),
          "FechaInicioVigencia"=>(string)$fecha_inicio,
          "FechaFinVigencia"=>(string) $fecha_fin,
          "EmpresaAdquirente"=>$row["cli_emp"],
          "ClienteCodigoEmpresa"=>$row["cli_cod_fam"],
          "CodigoDepartamento"=>$row["cod_dep"],
          "CodigoTipoCliente"=>$row["cli_tip_per_cod"],
          "ClienteDepartamento"=>$row["cli_dep"],
          "CodigoPlan"=>$row["plan_code"],
          "Clase"=>$row["clase"],
          "MontoPlan"=>$row["monto_plan_1"]
      ];



  } 
  else{
      $plan = array('datos'=>$row,'error'=>'EL PLAN NO EXISTE');
      array_push($errores,$plan);
    

      Log::info("Cliente: ".json_encode($errores));
    //  Log::info("Error: La sintaxis del campo Plan no es la correcta!. PLAN:".$row["plancliente"]);
  }


    
     
   


   
   if(count($existe)==0)
   {      
      

      if( strlen($row["cedula"])<=10)
      {
    
          $tipo_identificacion=2;
      }
      else
      {
          $tipo_identificacion=1;
      }

    
      $nombres = explode(" ", trim($row["cli_nom"])); 

//GUARDAR EL CLIENTE

      $cliente = new ClientesModel;
   
      $cliente->IdentificacionTipoId= $tipo_identificacion;
      $cliente->ClienteTipoId= $ClienteTipo[0]->ClienteTipoId;
      $cliente->SexoId=null;
      $cliente->CiudadId= null;
      $cliente->Identificacion= strval(TRIM($row["cedula"]));

      if(!empty($nombres[2]))
      {  
           $cliente->PrimerNombre= $nombres[2];   
      }
      if(!empty($nombres[3]))
      {
          
          if(!empty($nombres[4]))
      {
          
          $cliente->SegundoNombre= $nombres[3].' '.$nombres[4];
      }
      $cliente->SegundoNombre= $nombres[3];
      }
      if(!empty($nombres[0]))
      {
          $cliente->PrimerApellido= $nombres[0];
     
      }
      if(!empty($nombres[1]))
      {
          $cliente->SegundoApellido= $nombres[1];
     
      }  
   
      $cliente->NombreCompleto= $row["cli_nom"];
      $cliente->FechaNacimiento= (string)$fecha_nac;
      $cliente->Observacion= null;
      $cliente->EsVip= 1;
      $cliente->EsHabilitado= 1;
      $cliente->EsEliminado= 0;
      $cliente->FechaCreacion= date("Y-m-d H:i:s");
      $cliente->UsuarioCreacionId= 1720442;
      $cliente->FechaModificacion= date("Y-m-d H:i:s");
      $cliente->UsuarioModificacionId= 1720442;
   
  
      try {
          $cliente->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }

   
     
     $ClienteId=$cliente->ClienteId;

///GUARDAR LA DIRECCION
//$order = ClientesDireccionModel::find(\DB::table('Cliente.ClienteDireccion')->max('id'));
     // $id = DB::table('Cliente.ClienteDireccion')->max('ClienteDireccionId');

/*      $clienteDireccion = new ClientesDireccionModel;
     
      //$clienteDireccion->ClienteDireccionId=$id+1;
      $clienteDireccion->ClienteId=$ClienteId;
      $clienteDireccion->Direccion=$row["direccion"];
      $clienteDireccion->Numero=null;
      $clienteDireccion->Sector=null;
      $clienteDireccion->Referencia=null;
      $clienteDireccion->CodigoPostal=null;
      $clienteDireccion->Posicion=null;
      $clienteDireccion->EsHabilitado= 1;
      $clienteDireccion->EsEliminado= 0;
      $clienteDireccion->FechaCreacion= date("Y-m-d H:i:s");
      $clienteDireccion->UsuarioCreacionId= 1720442;
      $clienteDireccion->FechaModificacion= date("Y-m-d H:i:s");
      $clienteDireccion->UsuarioModificacionId= 1720442;
      try {
          $clienteDireccion->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }*/

     
/*
if(strlen($row["celular"])<10)
{
  $prefijo="02";
  $celular=$row["celular"];
}
else{
  $prefijo="09";
  $celular=substr($row["celular"], 2); 
  

}

      //GUARDAR TELEFONO
      $ClienteTelefonoId = DB::table('Cliente.ClienteTelefono')->max('ClienteTelefonoId');
      $clienteTelefono = new ClientesTelefonoModel;
      $clienteTelefono->ClienteTelefonoId=$ClienteTelefonoId+1;
      $clienteTelefono->ClienteId=$ClienteId;
      $clienteTelefono->Operadora=null;
      $clienteTelefono->Prefijo=$prefijo;
      $clienteTelefono->Numero=$celular;
      $clienteTelefono->Extension=null;
      $clienteTelefono->EsFavorito=1;
      $clienteTelefono->EsHabilitado= 1;
      $clienteTelefono->EsEliminado= 0;
      $clienteTelefono->FechaCreacion= date("Y-m-d H:i:s");
      $clienteTelefono->UsuarioCreacionId= 1720442;
      $clienteTelefono->FechaModificacion= date("Y-m-d H:i:s");
      $clienteTelefono->UsuarioModificacionId= 1720442;
   
      try {
          $clienteTelefono->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }*/
      

      //GUARDAR MAIL

/*      $ClienteEmailId = DB::table('Cliente.ClienteEmail')->max('ClienteEmailId');

      $clienteEmail= new ClientesEmailModel;
      $clienteEmail->ClienteEmailId=$ClienteEmailId+1;
      $clienteEmail->ClienteId=$ClienteId;
      $clienteEmail->Email=$row["email"];   
      $clienteEmail->EsFavorito=1;
      $clienteEmail->EsHabilitado= 1;
      $clienteEmail->EsEliminado= 0;
      $clienteEmail->FechaCreacion= date("Y-m-d H:i:s");
      $clienteEmail->UsuarioCreacionId= 1720442;
      $clienteEmail->FechaModificacion= date("Y-m-d H:i:s");
      $clienteEmail->UsuarioModificacionId= 1720442;
   
      try {
          $clienteEmail->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }*/
      


      //GUARDAR CLIENTE DATO TIPO
  
    $ClienteDatoTipo = DB::table('Cliente.ClienteDatoTipo')->max('ClienteDatoTipoId');

      $clienteDatoTipo= new ClientesDatoTipoModel;
      $clienteDatoTipo->ClienteDatoTipoId=$ClienteDatoTipo+1;
      $clienteDatoTipo->DatoTipoId=$DatoTipo[0]->DatoTipoId;
      $clienteDatoTipo->ClienteId=$ClienteId;
      $clienteDatoTipo->Dato=json_encode($Dato);   
      $clienteDatoTipo->EsHabilitado= 1;
      $clienteDatoTipo->EsEliminado= 0;
      $clienteDatoTipo->FechaCreacion= date("Y-m-d H:i:s");
      $clienteDatoTipo->UsuarioCreacionId= 1720442;
      $clienteDatoTipo->FechaModificacion= date("Y-m-d H:i:s");
      $clienteDatoTipo->UsuarioModificacionId= 1720442;
   
      try {
          $clienteDatoTipo->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }
     

      $ClienteDatoTipoId=$clienteDatoTipo->ClienteDatoTipoId;

      $ClienteEmpresaAplicacionId = DB::table('Cliente.ClienteEmpresaAplicacion')->max('ClienteEmpresaAplicacionId');
    
    
      $clienteEmpresaAplicacion= new ClientesEmpresaAplicacionModel;
      $clienteEmpresaAplicacion->ClienteEmpresaAplicacionId=$ClienteEmpresaAplicacionId+1;
      $clienteEmpresaAplicacion->ClienteDatoTipoId=$ClienteDatoTipoId;
      $clienteEmpresaAplicacion->ProductoId=$Producto[0]->ProductoId; 
      $clienteEmpresaAplicacion->EsHabilitado= 1;
      $clienteEmpresaAplicacion->EsEliminado= 0;
      $clienteEmpresaAplicacion->FechaCreacion= date("Y-m-d H:i:s");
      $clienteEmpresaAplicacion->UsuarioCreacionId= 1720442;
      $clienteEmpresaAplicacion->FechaModificacion= date("Y-m-d H:i:s");
      $clienteEmpresaAplicacion->UsuarioModificacionId= 1720442;
      try {
          $clienteEmpresaAplicacion->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }
      


  }

  }

// CORIS PALIG 

   /*  $localFile = Storage::disk('ftp_autonomi')->get('autonomi_sftp');
   
       $localFile = Storage::disk('ftp')->get('/autonomi_sftp/Proveedores/Coris/VENTASDIARIAS/ENERO/VENTASDIARIAS.xlsx');

      Storage::disk('local')->put('Autonomi.xlsx', $localFile);*/
    
     // $fecha_actual=date("F");
  
     $array = Excel::toArray(new ExcelImport, 'CORISPALIGENE.xlsx');
    
     foreach ($array[0] as $row) {
        
       

       

      $existe= DB::table('Cliente.Cliente')
      ->where('Identificacion','=',strval( $row["cli_ci"]))->get();

      $fecha_inicio=  \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_inicio'])->format('Y-m-d');//\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_inicio'])->format('Y-m-d');


      $fecha_fin= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_fin'])->format('Y-m-d'); 
      
      $fecha_nac= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['cli_fec_nac'])->format('Y-m-d'); 
   

      
    
 


  
    $ClienteTipo= DB::table('Cliente.ClienteTipo')
    ->where('Nombre','=', 'TITULAR')->get();

      $DatoTipo= DB::table('Configuracion.DatoTipo')
      ->where('Nombre','=', 'DatoAsistenciaPanamericanLife')->get();


      //SACAR BIENE EL PLAN 
      $PlanId= DB::table('Empresa.Plan')
      ->where('Nombre','like', 'PLAN PALIG '.$row['plan_code'])->get();

      $Producto= DB::table('Empresa.Producto')
      ->where('Nombre','=', 'ASISTENCIA MEDICA INTERNACIONAL')->get();


  if(count($PlanId)>0)
  {
     
      $Dato=[
          "PlanId"=> strval($PlanId[0]->PlanId),
          "FechaInicioVigencia"=>(string)$fecha_inicio,
          "FechaFinVigencia"=>(string) $fecha_fin,
          "EmpresaAdquirente"=>$row["cli_emp"],
          "ClienteCodigoEmpresa"=>$row["cli_cod_fam"],
          "CodigoDepartamento"=>$row["cod_dep"],
          "CodigoTipoCliente"=>$row["cli_tip_per_cod"],
          "ClienteDepartamento"=>$row["cli_dep"],
          "CodigoPlan"=>$row["plan_code"],
          "Clase"=>$row["clase"],
          "MontoPlan"=>$row["monto_plan_1"]
      ];



  } 
  else{
      $plan = array('datos'=>$row,'error'=>'EL PLAN NO EXISTE');
      array_push($errores,$plan);
    

      Log::info("Cliente: ".json_encode($errores));
    //  Log::info("Error: La sintaxis del campo Plan no es la correcta!. PLAN:".$row["plancliente"]);
  }


    
     
   


   
   if(count($existe)==0)
   {      
      

      if( strlen($row["cedula"])<=10)
      {
    
          $tipo_identificacion=2;
      }
      else
      {
          $tipo_identificacion=1;
      }

    
      $nombres = explode(" ", trim($row["cli_nom"])); 

//GUARDAR EL CLIENTE

      $cliente = new ClientesModel;
   
      $cliente->IdentificacionTipoId= $tipo_identificacion;
      $cliente->ClienteTipoId= $ClienteTipo[0]->ClienteTipoId;
      $cliente->SexoId=null;
      $cliente->CiudadId= null;
      $cliente->Identificacion= strval(TRIM($row["cedula"]));

      if(!empty($nombres[2]))
      {  
           $cliente->PrimerNombre= $nombres[2];   
      }
      if(!empty($nombres[3]))
      {
          
          if(!empty($nombres[4]))
      {
          
          $cliente->SegundoNombre= $nombres[3].' '.$nombres[4];
      }
      $cliente->SegundoNombre= $nombres[3];
      }
      if(!empty($nombres[0]))
      {
          $cliente->PrimerApellido= $nombres[0];
     
      }
      if(!empty($nombres[1]))
      {
          $cliente->SegundoApellido= $nombres[1];
     
      }  
   
      $cliente->NombreCompleto= $row["cli_nom"];
      $cliente->FechaNacimiento= (string)$fecha_nac;
      $cliente->Observacion= null;
      $cliente->EsVip= 1;
      $cliente->EsHabilitado= 1;
      $cliente->EsEliminado= 0;
      $cliente->FechaCreacion= date("Y-m-d H:i:s");
      $cliente->UsuarioCreacionId= 1720442;
      $cliente->FechaModificacion= date("Y-m-d H:i:s");
      $cliente->UsuarioModificacionId= 1720442;
   
  
      try {
          $cliente->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }

   
     
     $ClienteId=$cliente->ClienteId;

///GUARDAR LA DIRECCION
//$order = ClientesDireccionModel::find(\DB::table('Cliente.ClienteDireccion')->max('id'));
     // $id = DB::table('Cliente.ClienteDireccion')->max('ClienteDireccionId');

/*      $clienteDireccion = new ClientesDireccionModel;
     
      //$clienteDireccion->ClienteDireccionId=$id+1;
      $clienteDireccion->ClienteId=$ClienteId;
      $clienteDireccion->Direccion=$row["direccion"];
      $clienteDireccion->Numero=null;
      $clienteDireccion->Sector=null;
      $clienteDireccion->Referencia=null;
      $clienteDireccion->CodigoPostal=null;
      $clienteDireccion->Posicion=null;
      $clienteDireccion->EsHabilitado= 1;
      $clienteDireccion->EsEliminado= 0;
      $clienteDireccion->FechaCreacion= date("Y-m-d H:i:s");
      $clienteDireccion->UsuarioCreacionId= 1720442;
      $clienteDireccion->FechaModificacion= date("Y-m-d H:i:s");
      $clienteDireccion->UsuarioModificacionId= 1720442;
      try {
          $clienteDireccion->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }*/

     
/*
if(strlen($row["celular"])<10)
{
  $prefijo="02";
  $celular=$row["celular"];
}
else{
  $prefijo="09";
  $celular=substr($row["celular"], 2); 
  

}

      //GUARDAR TELEFONO
      $ClienteTelefonoId = DB::table('Cliente.ClienteTelefono')->max('ClienteTelefonoId');
      $clienteTelefono = new ClientesTelefonoModel;
      $clienteTelefono->ClienteTelefonoId=$ClienteTelefonoId+1;
      $clienteTelefono->ClienteId=$ClienteId;
      $clienteTelefono->Operadora=null;
      $clienteTelefono->Prefijo=$prefijo;
      $clienteTelefono->Numero=$celular;
      $clienteTelefono->Extension=null;
      $clienteTelefono->EsFavorito=1;
      $clienteTelefono->EsHabilitado= 1;
      $clienteTelefono->EsEliminado= 0;
      $clienteTelefono->FechaCreacion= date("Y-m-d H:i:s");
      $clienteTelefono->UsuarioCreacionId= 1720442;
      $clienteTelefono->FechaModificacion= date("Y-m-d H:i:s");
      $clienteTelefono->UsuarioModificacionId= 1720442;
   
      try {
          $clienteTelefono->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }*/
      

      //GUARDAR MAIL

/*      $ClienteEmailId = DB::table('Cliente.ClienteEmail')->max('ClienteEmailId');

      $clienteEmail= new ClientesEmailModel;
      $clienteEmail->ClienteEmailId=$ClienteEmailId+1;
      $clienteEmail->ClienteId=$ClienteId;
      $clienteEmail->Email=$row["email"];   
      $clienteEmail->EsFavorito=1;
      $clienteEmail->EsHabilitado= 1;
      $clienteEmail->EsEliminado= 0;
      $clienteEmail->FechaCreacion= date("Y-m-d H:i:s");
      $clienteEmail->UsuarioCreacionId= 1720442;
      $clienteEmail->FechaModificacion= date("Y-m-d H:i:s");
      $clienteEmail->UsuarioModificacionId= 1720442;
   
      try {
          $clienteEmail->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }*/
      


      //GUARDAR CLIENTE DATO TIPO
  
    $ClienteDatoTipo = DB::table('Cliente.ClienteDatoTipo')->max('ClienteDatoTipoId');

      $clienteDatoTipo= new ClientesDatoTipoModel;
      $clienteDatoTipo->ClienteDatoTipoId=$ClienteDatoTipo+1;
      $clienteDatoTipo->DatoTipoId=$DatoTipo[0]->DatoTipoId;
      $clienteDatoTipo->ClienteId=$ClienteId;
      $clienteDatoTipo->Dato=json_encode($Dato);   
      $clienteDatoTipo->EsHabilitado= 1;
      $clienteDatoTipo->EsEliminado= 0;
      $clienteDatoTipo->FechaCreacion= date("Y-m-d H:i:s");
      $clienteDatoTipo->UsuarioCreacionId= 1720442;
      $clienteDatoTipo->FechaModificacion= date("Y-m-d H:i:s");
      $clienteDatoTipo->UsuarioModificacionId= 1720442;
   
      try {
          $clienteDatoTipo->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }
     

      $ClienteDatoTipoId=$clienteDatoTipo->ClienteDatoTipoId;

      $ClienteEmpresaAplicacionId = DB::table('Cliente.ClienteEmpresaAplicacion')->max('ClienteEmpresaAplicacionId');
    
    
      $clienteEmpresaAplicacion= new ClientesEmpresaAplicacionModel;
      $clienteEmpresaAplicacion->ClienteEmpresaAplicacionId=$ClienteEmpresaAplicacionId+1;
      $clienteEmpresaAplicacion->ClienteDatoTipoId=$ClienteDatoTipoId;
      $clienteEmpresaAplicacion->ProductoId=$Producto[0]->ProductoId; 
      $clienteEmpresaAplicacion->EsHabilitado= 1;
      $clienteEmpresaAplicacion->EsEliminado= 0;
      $clienteEmpresaAplicacion->FechaCreacion= date("Y-m-d H:i:s");
      $clienteEmpresaAplicacion->UsuarioCreacionId= 1720442;
      $clienteEmpresaAplicacion->FechaModificacion= date("Y-m-d H:i:s");
      $clienteEmpresaAplicacion->UsuarioModificacionId= 1720442;
      try {
          $clienteEmpresaAplicacion->save();
      } catch (Throwable $e) {
          Log::info($e);
  
       
      }
      


  }

  }


}

}






   

  



