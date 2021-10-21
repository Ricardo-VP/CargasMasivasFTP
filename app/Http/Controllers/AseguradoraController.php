<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\ClientesModel;
// use App\ClientesDireccionModel;
// use App\ClientesTelefonoModel;
// use App\ClientesEmailModel;
// use App\ClientesDatoTipoModel;
// use App\ClientesEmpresaAplicacionModel;
// use App\Imports\ExcelImport;
// use Illuminate\Support\Facades\DB;
// use Maatwebsite\Excel\Facades\Excel;
// use File;
// use App\Clientes;
// use Carbon\Carbon;
// use DateTime;
// use Log;

// use Illuminate\Support\Facades\Storage;



// class AseguradoraController extends Controller
// {
  
//     public function ImportarDatosAseguradora()
//     {
       
//        //localFile = Storage::disk('ftp')->get('/DataSource/Autonomi/Autonomi.xlsx');

//       //Storage::disk('local')->put('Autonomi.xlsx', $localFile);
//        $array = Excel::toArray(new ExcelImport, 'Aseguradora.xlsx');
       
//       foreach ($array[0] as $row) {
           
 
//     $fecha_inicio= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['vigencia_inicial'])->format('Y-m-d');
//     $fecha_fin= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['vigencia_final'])->format('Y-m-d');
//     $fecha_fact= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_facturacion'])->format('Y-m-d');
//     $fecha_emision= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_emision'])->format('Y-m-d');
 

// //DESCOMPONER EL NOMBRE

//         $nombres = explode(" ", trim($row["beneficiario"])); 
    
//         $existe= DB::table('Cliente.Cliente')->where('NombreCompleto','like','%'.trim($row["beneficiario"]).'%')->get();
//         $existe_cedula= DB::table('Cliente.Cliente')->where('Identificacion','like','%'.trim($row["identificacion_solicitante"]).'%' )->get();
   
   

//       $ClienteTipo= DB::table('Cliente.ClienteTipo')
//       ->where('Nombre','=', 'TITULAR')->get();
     

//         $DatoTipo= DB::table('Configuracion.DatoTipo')
//         ->where('Nombre','=', 'DatoAseguradoradelSur')->get();
     
        
//         $PlanId= DB::table('Empresa.Plan')
//         ->where('Nombre','like', '%'.trim($row["plan"]).'%')->get();
        
    
//         $Producto= DB::table('Empresa.Producto')
//         ->where('Nombre','=', 'ASISTENCIA ASEGURADORA DEL SUR')->get();

//         $TipoAsistencia= DB::table('Configuracion.Prestacion')
//         ->where('Nombre','=', 'MEDICA')->get();

   
//         $ClienteDatoTipo= DB::table('Cliente.ClienteDatoTipo')
//         ->where('ClienteId','=', $ClienteId)->get();
//         $clienteDatoTipoId=$ClienteDatoTipo[0].["ClienteDatoTipoId"]


//         if( trim($row["beneficiario"]) == trim($row["solicitante"]))
//         {
            
//             $solicitante=trim($row["beneficiario"]);
//             $identificacion=  trim($row["identificacion_solicitante"]);

//             if( strlen(trim($row["identificacion_solicitante"]))>10)
//             {
          
//                 $tipo_identificacion=2;
//             }
//             else
//             {
//                 $tipo_identificacion=1;
//             }
//         }
//         else
//         {
            
//             $solicitante=trim($row["solicitante"]);
//             $identificacion=  null;
//         }

//         $Dato=[
//             "PlanId"=> intval(trim($PlanId[0]->PlanId)),
//             "CodigoRamo"=>trim($row["codigo_ramo"]),
//             "Ramo"=>trim($row["ramo"]),
//             "Poliza"=>trim($row["poliza"]),
//             "IdentificacionSolicitante"=>trim($row["identificacion_solicitante"]),
//             "Solicitante"=>$solicitante,
//             "FechaEmision"=> strval($fecha_emision),
//             "FechaInicio"=>strval($fecha_inicio),
//             "FechaFin"=>strval($fecha_fin),
//             "LineaNegocio"=> trim($row["linea_negocio"]) ,
//             "Canal"=>trim($row["canal"]),
//             "Orden"=>trim($row["orden"]),
//             "Anexo"=>trim($row["anexo"]),
//             "Endoso"=>trim($row["endoso"]),
//             "NumeroItem"=>trim($row["numero_item"]),
//             "SumaAsegurada"=>trim($row["suma_asegurada"]),
//             "PrimaNeta"=>trim($row["prima_neta"]),
//             "Sucursal"=>trim($row["sucursal"]),
//             "RucAPS"=>trim($row["ruc_aps"]),
//             "NombreAPS"=>trim($row["nombre_aps"]),
//             "SistemaFuente"=>trim($row["sistema_fuente"]),
//             "FechaFacturacion"=>strval($fecha_fact),
//             "Estado"=>trim($row["estado"]),
//             "AsistenciaTipoId"=>intval($TipoAsistencia[0]->PrestacionId)

    
//         ];
     
 

     
//      if(count($existe)==0 || count($existe_cedula)==0 )
//      {
     
       
        

//         if( strlen(trim($row["identificacion_solicitante"]))>10)
//         {
        
      
//             $tipo_identificacion=2;
//         }
//         else
//         {
//             $tipo_identificacion=1;
//         }
 
      
        
// //GUARDAR EL CLIENTE

// //BUSCAR CIUDAD 

//         $cliente = new ClientesModel;
     
//         $cliente->IdentificacionTipoId= $tipo_identificacion;
//         $cliente->ClienteTipoId= $ClienteTipo[0]->ClienteTipoId;
        
//         $cliente->SexoId=null;
//         $cliente->CiudadId= null;
//         $cliente->Identificacion= $identificacion;
     
//         if(!empty($nombres[2]))
//         {  
//              $cliente->PrimerNombre= $nombres[2];   
//         }
//         if(!empty($nombres[3]))
//         {
            
//             if(!empty($nombres[4]))
//         {
            
//             $cliente->SegundoNombre= $nombres[3].' '.$nombres[4];
//         }
//         $cliente->SegundoNombre= $nombres[3];
//         }
//         if(!empty($nombres[0]))
//         {
//             $cliente->PrimerApellido= $nombres[0];
       
//         }
//         if(!empty($nombres[1]))
//         {
//             $cliente->SegundoApellido= $nombres[1];
       
//         }     
        
//         $cliente->NombreCompleto= trim($row["beneficiario"]);
//         $cliente->FechaNacimiento= null;
//         $cliente->Observacion= null;
//         $cliente->EsVip= 1;
//         $cliente->EsHabilitado= 1;
//         $cliente->EsEliminado= 0;
//         $cliente->FechaCreacion= date("Y-m-d H:i:s");
//         $cliente->UsuarioCreacionId= 1720442;
//         $cliente->FechaModificacion= date("Y-m-d H:i:s");
//         $cliente->UsuarioModificacionId= 1720442;
    
//         try {
//             $cliente->save();
//         } catch (Throwable $e) {
//             Log::info($e);
    
         
//         }
 
       
//         $ClienteId=$cliente->ClienteId;


//         //GUARDAR CLIENTE DATO TIPO
    
//       $ClienteDatoTipo = DB::table('Cliente.ClienteDatoTipo')->max('ClienteDatoTipoId');

//         $clienteDatoTipo= new ClientesDatoTipoModel;
//         $clienteDatoTipo->ClienteDatoTipoId=$ClienteDatoTipo+1;
//         $clienteDatoTipo->DatoTipoId=$DatoTipo[0]->DatoTipoId;
       
//         $clienteDatoTipo->ClienteId=$ClienteId;
//         $clienteDatoTipo->Dato=json_encode($Dato);   
//         $clienteDatoTipo->EsHabilitado= 1;
//         $clienteDatoTipo->EsEliminado= 0;
//         $clienteDatoTipo->FechaCreacion= date("Y-m-d H:i:s");
//         $clienteDatoTipo->UsuarioCreacionId= 1720442;
//         $clienteDatoTipo->FechaModificacion= date("Y-m-d H:i:s");
//         $clienteDatoTipo->UsuarioModificacionId= 1720442;
//         try {
//             $clienteDatoTipo->save();
//         } catch (Throwable $e) {
//             Log::info($e);
    
         
//         }
     
        

//         $ClienteDatoTipoId=$clienteDatoTipo->ClienteDatoTipoId;

//         $ClienteEmpresaAplicacionId = DB::table('Cliente.ClienteEmpresaAplicacion')->max('ClienteEmpresaAplicacionId');
      
      
//         $clienteEmpresaAplicacion= new ClientesEmpresaAplicacionModel;
//         $clienteEmpresaAplicacion->ClienteEmpresaAplicacionId=$ClienteEmpresaAplicacionId+1;
//         $clienteEmpresaAplicacion->ClienteDatoTipoId=$ClienteDatoTipoId;
//         $clienteEmpresaAplicacion->ProductoId=$Producto[0]->ProductoId; 
//         $clienteEmpresaAplicacion->EsHabilitado= 1;
//         $clienteEmpresaAplicacion->EsEliminado= 0;
//         $clienteEmpresaAplicacion->FechaCreacion= date("Y-m-d H:i:s");
//         $clienteEmpresaAplicacion->UsuarioCreacionId= 1720442;
//         $clienteEmpresaAplicacion->FechaModificacion= date("Y-m-d H:i:s");
//         $clienteEmpresaAplicacion->UsuarioModificacionId= 1720442;
     
//         try {
//             $clienteEmpresaAplicacion->save();
//         } catch (Throwable $e) {
//             Log::info($e);
    
         
//         }
        


//     }
 
//     }

// ///CORREO ELECTRONICA
// //select a la tabla de correos de operadores
// ///AEGURADORA DEL SUR
// // OPERADOR operador1@aseguradora.ec
// ///del error salta a la siguiente línea next al siguiente usuario
// ///enviar correo sobre los datos que se subieron
// ///si se sube bien indicar que la carga se ejecutó correctemnte
// /// si existe algun error enviarlo y notificar 


// ///HOGAR

// //Log:info("REgistrando Clientes MEdicos Aseguradora Del Sur....");
// foreach ($array[1] as $row) {


   
//     $fecha_inicio= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['vig_ini_poliza'])->format('Y-m-d');
//     $fecha_fin= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['vig_ini_poliza'])->format('Y-m-d');
//     $fecha_fact= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_facturacion'])->format('Y-m-d');
//     $fecha_emision= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_emision'])->format('Y-m-d');
 


      
//         $nombres = explode(" ", trim($row["beneficiario"])); 
    
//         $existe= DB::table('Cliente.Cliente')->where('NombreCompleto','like','%'.trim($row["beneficiario"]).'%')->get();
//         $existe_cedula= DB::table('Cliente.Cliente')->where('Identificacion','like','%'.trim($row["id_benef"]).'%' )->get();
   
   

//       $ClienteTipo= DB::table('Cliente.ClienteTipo')
//       ->where('Nombre','=', 'TITULAR')->get();
     

//         $DatoTipo= DB::table('Configuracion.DatoTipo')
//         ->where('Nombre','=', 'DatoAseguradoradelSur')->get();
     
//         //REVISAR
//         if(trim($row["plan"]==''))
//         {
//             $PlanId= DB::table('Empresa.Plan')->where('Nombre','like', '%NO PLAN%')->get();

//         }

//         $PlanId= DB::table('Empresa.Plan')->where('Nombre','like', '%'.trim($row["plan"]).'%')->get();       
      
    
//         $Producto= DB::table('Empresa.Producto')
//         ->where('Nombre','=', 'INCENDIO')->get();
      
//         //REVISAR
//         $TipoAsistencia= DB::table('Configuracion.Prestacion')
//         ->where('Nombre','=', 'HOGAR')->get();




//         if( trim($row["id_benef"])==trim($row["id_solici"]))
//         {
           
            
//             $solicitante=trim($row["solicitante"]);         
//             $identificacion= trim($row["id_solici"]);   

         
//              if( intval(strlen($row["id_solici"]))<=10)
//             {
         
          
//                 $tipo_identificacion=1;
//             }
//             else
//             {
//                 $tipo_identificacion=2;
//             }

         
//         }
//         else
//         {
//             $solicitante=trim($row["beneficiario"]);         
//             $identificacion= trim($row["id_benef"]);     
            


//         }

//         $Dato=[
//             "PlanId"=> intval(trim($PlanId[0]->PlanId)),
//             "CodigoRamo"=>trim($row["codigo_ramo"]),
//             "Ramo"=>trim($row["ramo"]),
//             "Poliza"=>trim($row["poliza"]),
//             "IdentificacionSolicitante"=>trim($row["id_solici"]),
//             "Solicitante"=>$solicitante,
//             "FechaEmision"=> strval($fecha_emision),
//             "FechaInicio"=>strval($fecha_inicio),
//             "FechaFin"=>strval($fecha_fin),
//             "LineaNegocio"=> trim($row["linea_negocio"]) ,
//             "Canal"=>trim($row["canal"]),
//             "GrupoEconomico"=>trim($row["grupo_economico"]),
//             "SumaAsegurada"=>trim($row["suma_asegurada"]),
//             "PrimaNeta"=>trim($row["prima_neta"]),
//             "Endoso"=>trim($row["endoso"]),
//             "NumeroAnexo"=>trim($row["num_anexo"]),
//             "Orden"=>trim($row["orden"]),           
//             "NumeroItem"=>trim($row["num_item"]),
            
//             "Sucursal"=>trim($row["nom_sucursal"]),
//             "RucAPS"=>trim($row["ruc_aps"]),
//             "NombreAPS"=>trim($row["nombre_aps"]),
//             "SistemaFuente"=>trim($row["fuente"]),
//             "FechaFacturacion"=>strval($fecha_fact),
//             "Estado"=>trim($row["estado"]),
//             "AsistenciaTipoId"=>intval($TipoAsistencia[0]->PrestacionId)

//         ];
     
 

     
//      if(count($existe_cedula)==0 )
//      {
     
       
        

//         if( strlen(trim($row["id_benef"]))<=10)
//         {
        
      
//             $tipo_identificacion=1;
//         }
//         else
//         {
//             $tipo_identificacion=2;
//         }
 
   
        
// //GUARDAR EL CLIENTE
// //dd(trim($row["nom_sucursal"]));
// $ciudad= DB::table('Catalogo.Ciudad')->where('Nombre','=',trim($row["nom_sucursal"]))->get();
// if(count($ciudad)!=0)
// {
//    $CiudadId= $ciudad[0]->CiudadId;
// }
// else{
//     $CiudadId= null;

// }



// //dd(intval($ClienteTipo[0]->ClienteTipoId));
//         $cliente = new ClientesModel;
     
//         $cliente->IdentificacionTipoId= $tipo_identificacion;
//         $cliente->ClienteTipoId= intval($ClienteTipo[0]->ClienteTipoId);
        
//         $cliente->SexoId=null;
//         $cliente->CiudadId= $CiudadId;
//         $cliente->Identificacion= $identificacion;
     
//         if(!empty($nombres[2]))
//         {  
//              $cliente->PrimerNombre= $nombres[2];   
//         }
//         if(!empty($nombres[3]))
//         {
            
//             if(!empty($nombres[4]))
//         {
            
//             $cliente->SegundoNombre= $nombres[3].' '.$nombres[4];
//         }
//         $cliente->SegundoNombre= $nombres[3];
//         }
//         if(!empty($nombres[0]))
//         {
//             $cliente->PrimerApellido= $nombres[0];
       
//         }
//         if(!empty($nombres[1]))
//         {
//             $cliente->SegundoApellido= $nombres[1];
       
//         }     
        
//         $cliente->NombreCompleto= trim($row["beneficiario"]);
//         $cliente->FechaNacimiento= null;
//         $cliente->Observacion= null;
//         $cliente->EsVip= 1;
//         $cliente->EsHabilitado= 1;
//         $cliente->EsEliminado= 0;
//         $cliente->FechaCreacion= date("Y-m-d H:i:s");
//         $cliente->UsuarioCreacionId= 1720442;
//         $cliente->FechaModificacion= date("Y-m-d H:i:s");
//         $cliente->UsuarioModificacionId= 1720442;
    
//         try {
//             $cliente->save();
       
//         } catch (Throwable $e) {
//             Log::info($e);
    
         
//         }
       
//         $ClienteId=$cliente->ClienteId;
  

// ///GUARDAR LA DIRECCION
// //$order = ClientesDireccionModel::find(\DB::table('Cliente.ClienteDireccion')->max('id'));
//         //$id = DB::table('Cliente.ClienteDireccion')->max('ClienteDireccionId');

//         $clienteDireccion = new ClientesDireccionModel;
       
//         //$clienteDireccion->ClienteDireccionId=$id+1;
//         $clienteDireccion->ClienteId=$ClienteId;
//         $clienteDireccion->Direccion=$row["ubicacion"];
//         $clienteDireccion->Numero=null;
//         $clienteDireccion->Sector=null;
//         $clienteDireccion->Referencia=null;
//         $clienteDireccion->CodigoPostal=null;
//         $clienteDireccion->Posicion=null;
//         $clienteDireccion->EsHabilitado= 1;
//         $clienteDireccion->EsEliminado= 0;
//         $clienteDireccion->FechaCreacion= date("Y-m-d H:i:s");
//         $clienteDireccion->UsuarioCreacionId= 1720442;
//         $clienteDireccion->FechaModificacion= date("Y-m-d H:i:s");
//         $clienteDireccion->UsuarioModificacionId= 1720442;
     
        
//         try {
//             $clienteDireccion->save();
       
//         } catch (Throwable $e) {
//             Log::info($e);
    
         
//         }
     


//         //GUARDAR TELEFONO
//        /* $ClienteTelefonoId = DB::table('Cliente.ClienteTelefono')->max('ClienteTelefonoId');
//         $clienteTelefono = new ClientesTelefonoModel;
//         $clienteTelefono->ClienteTelefonoId=$ClienteTelefonoId+1;
//         $clienteTelefono->ClienteId=$ClienteId;
//         $clienteTelefono->Operadora=null;
//         $clienteTelefono->Prefijo=$prefijo;
//         $clienteTelefono->Numero=$celular;
//         $clienteTelefono->Extension=null;
//         $clienteTelefono->EsFavorito=1;
//         $clienteTelefono->EsHabilitado= 1;
//         $clienteTelefono->EsEliminado= 0;
//         $clienteTelefono->FechaCreacion= date("Y-m-d H:i:s");
//         $clienteTelefono->UsuarioCreacionId= 1720442;
//         $clienteTelefono->FechaModificacion= date("Y-m-d H:i:s");
//         $clienteTelefono->UsuarioModificacionId= 1720442;
     
//         $clienteTelefono->save();*/

//         //GUARDAR MAIL

//      /*   $ClienteEmailId = DB::table('Cliente.ClienteEmail')->max('ClienteEmailId');

//         $clienteEmail= new ClientesEmailModel;
//         $clienteEmail->ClienteEmailId=$ClienteEmailId+1;
//         $clienteEmail->ClienteId=$ClienteId;
//         $clienteEmail->Email=$row["email"];   
//         $clienteEmail->EsFavorito=1;
//         $clienteEmail->EsHabilitado= 1;
//         $clienteEmail->EsEliminado= 0;
//         $clienteEmail->FechaCreacion= date("Y-m-d H:i:s");
//         $clienteEmail->UsuarioCreacionId= 1720442;
//         $clienteEmail->FechaModificacion= date("Y-m-d H:i:s");
//         $clienteEmail->UsuarioModificacionId= 1720442;
     
//         $clienteEmail->save();*/


//         //GUARDAR CLIENTE DATO TIPO

//       $ClienteDatoTipo = DB::table('Cliente.ClienteDatoTipo')->max('ClienteDatoTipoId');

//         $clienteDatoTipo= new ClientesDatoTipoModel;
//         $clienteDatoTipo->ClienteDatoTipoId=$ClienteDatoTipo+1;
//         $clienteDatoTipo->DatoTipoId=$DatoTipo[0]->DatoTipoId;
       
//         $clienteDatoTipo->ClienteId=$ClienteId;
//         $clienteDatoTipo->Dato=json_encode($Dato);   
//         $clienteDatoTipo->EsHabilitado= 1;
//         $clienteDatoTipo->EsEliminado= 0;
//         $clienteDatoTipo->FechaCreacion= date("Y-m-d H:i:s");
//         $clienteDatoTipo->UsuarioCreacionId= 1720442;
//         $clienteDatoTipo->FechaModificacion= date("Y-m-d H:i:s");
//         $clienteDatoTipo->UsuarioModificacionId= 1720442;
     
//         try {
//             $clienteDatoTipo->save();
       
//         } catch (Throwable $e) {
//             Log::info($e);
    
         
//         }
       

//         $ClienteDatoTipoId=$clienteDatoTipo->ClienteDatoTipoId;

//         $ClienteEmpresaAplicacionId = DB::table('Cliente.ClienteEmpresaAplicacion')->max('ClienteEmpresaAplicacionId');
      
    
//         $clienteEmpresaAplicacion= new ClientesEmpresaAplicacionModel;
//         $clienteEmpresaAplicacion->ClienteEmpresaAplicacionId=$ClienteEmpresaAplicacionId+1;
//         $clienteEmpresaAplicacion->ClienteDatoTipoId=$ClienteDatoTipoId;
//         $clienteEmpresaAplicacion->ProductoId=$Producto[0]->ProductoId; 
//         $clienteEmpresaAplicacion->EsHabilitado= 1;
//         $clienteEmpresaAplicacion->EsEliminado= 0;
//         $clienteEmpresaAplicacion->FechaCreacion= date("Y-m-d H:i:s");
//         $clienteEmpresaAplicacion->UsuarioCreacionId= 1720442;
//         $clienteEmpresaAplicacion->FechaModificacion= date("Y-m-d H:i:s");
//         $clienteEmpresaAplicacion->UsuarioModificacionId= 1720442;
     
//         try {
//             $clienteEmpresaAplicacion->save();
       
//         } catch (Throwable $e) {
            
//             Log::info($e);
    
         
//         }
       


//     }

// }


// echo "Datos Insertados Correctamente!";
      
//     }
    

  


// }
