<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\CatTopico as Topico;
use App\Models\CatPrivacidadDocumento as Privacidad;
use App\Models\CatTipoDocumento as TipoDoc;
use App\Models\CatArea as Area;
use App\Models\CatModulo as Modulo;
use App\Models\CatUbicacion as Ubicacion;
use App\Models\Documentod as Documentod;
class DocumentosPublicosController extends Controller
{
    protected function getCarpetas(Request $request){

      if(Auth::check()){
        $ubicacionId= Ubicacion::firstWhere('descripcion',$request["ubicacion"]);
        $res=  DB::table('documento')
        ->where('borrado', '0')->where("esCarpeta",'1')->where("documentoPadre",'0')->orderBy('titulo', 'asc')->get();
      $path= array();
        $path = $res;
        $myArray = [];
        $arrayCarpetas=[];
        $arrayArchivos=[];
        $arrayMeta=[];
        $arrayUbicacion=[];

        foreach($path as $dir){

            array_push( $arrayCarpetas,(object)[
              'documentoid'=> $dir->documentoid,
              'documentoPadre'=> $dir->documentoPadre,
              'titulo'=> $dir->titulo

            ]);
        }
        $script=('Select * from documento as a inner join documentod as b on a.documentoid=b.documentoId LEFT JOIN cat_privacidad_documento as c on b.privacidadDocumentoId= c.privacidadDocumentoId left join
        documento_ubicacion as d on b.documentodId=d.documentodId
        where a.documentoPadre=0 and a.borrado=0 and b.borrado=0 and b.privacidadDocumentoId=3 and d.ubicacionId='.$ubicacionId->ubicacionId.' order by a.titulo asc;
        ');

        $sql=DB::select($script);




        foreach($sql as $dir){
            $filename=$dir->iconoDocumento;
            $icono=asset('storage/Publico/iconos/'. $filename);
            $header = [
                'Content-Type' => 'image/jpg',
            ];
            $filen=$dir->archivo;
            $file=asset('storage/Publico/'. $filen);

             //Array metadatos
        $resMetadato=DB::select('select * from documento_metadato where documentodId='.$dir->documentodId.' and borrado=0;');
        $resUbicacion=DB::select('select * from documento_ubicacion where documentodId='.$dir->documentodId.' and borrado=0;');

        foreach($resMetadato as $metadato){
          array_push( $arrayMeta,(object)[
            'documentoMetadatoId' => $metadato->documentoMetadatoId,
            'descripcion' => $metadato->descripcion
          ]);
        }

        foreach($resUbicacion as $ubicacion){
          array_push( $arrayUbicacion,(object)[
            'documentoUbicacionId' => $ubicacion->documentoUbicacionId,
            'ubicacionId' => $ubicacion->ubicacionId
          ]);
        }
            array_push( $arrayArchivos,(object)[
              'id'=> $dir->documentodId,
              'documentoId'=> $dir->documentoId,
              'documentoPadre'=> $dir->documentoPadre,
              'tipoDocid'=>$dir->tipoDocumentoId,
              'topicoid'=> $dir->topicoID,
              'privacidadid'=> $dir->privacidadDocumentoId,
              'privacidadText' => $dir->descripcion,
              'areaId'=> $dir->areaId,
              'moduloId'=> $dir->moduloId,
              'titulo'=> $dir->titulo,
              'archivo'=> $dir->archivo,
              'urlArchivo'=> $file,
              'extension'=> $dir->extension,
              'size'=> $this->formatBytes($dir->tamanio),
              'numeroConsultas'=> $dir->numeroConsultas,
              'numeroDescargas' => $dir->numeroDescargas,
              'fechaCaptura' => $dir->fechaCaptura,
              'fechaUltimaConsulta' => $dir->fechaUltimaConsulta,
              'fechaUltimaDescarga' => $dir->fechaUltimaDescarga,
              'icono' => $icono,
              'icono_name'=> $dir->iconoDocumento,
              'metadatos'=> $arrayMeta,
              'ubicacion' => $arrayUbicacion
                ]);


              $arrayMeta=[];
              $arrayUbicacion=[];
              }

              array_push( $myArray,(object)[
                'carpetas'=> $arrayCarpetas,
                'archivos'=> $arrayArchivos
              ]);

        return response()->json($myArray, 200);
      }else{
        return response()->json(null, 500); 

    }
      }

    protected function getSubCarpetas(Request $request){
      if(Auth::check()){
        $ubicacionId= Ubicacion::firstWhere('descripcion',$request["ubicacion"]);
       $res=  DB::table('documento')
      ->where('borrado', '0')->where("esCarpeta",'1')->where('documentoPadre','=',$request["documentoPadre"])->orderBy('titulo', 'asc')->get();

      $sql=DB::select('Select * from documento as a inner join documentod as b on a.documentoid=b.documentoId LEFT JOIN cat_privacidad_documento as c on b.privacidadDocumentoId= c.privacidadDocumentoId left join
      documento_ubicacion as d on b.documentodId=d.documentodId
      where a.documentoPadre='.$request["documentoPadre"].' and a.borrado=0 and b.borrado=0 and b.privacidadDocumentoId=3 and d.ubicacionId='.$ubicacionId->ubicacionId.' order by a.titulo asc;
      ');


    // $path= array();
    //   $path = $res;
    //    echo $path;
      $myArray = [];
      $arrayCarpetas=[];
      $arrayArchivos=[];
      $arrayMeta=[];
      $arrayUbicacion=[];
      foreach($res as $dir){

      array_push($arrayCarpetas,(object)[
          'documentoid'=> $dir->documentoid,
          'documentoPadre'=> $dir->documentoPadre,
          'titulo'=> $dir->titulo

          ]);
      }

      foreach($sql as $dir){
      $filename=$dir->iconoDocumento;

      $icono=asset('storage/Publico/iconos/'. $filename);


      $header = [
          'Content-Type' => 'image/jpg',
      ];
      $filen=$dir->archivo;
      $file=asset('storage/Publico/'. $filen);

      //Array metadatos
      $resMetadato=DB::select('select * from documento_metadato where documentodId='.$dir->documentodId.' and borrado=0;');
      $resUbicacion=DB::select('select * from documento_ubicacion where documentodId='.$dir->documentodId.' and borrado=0;');

      foreach($resMetadato as $metadato){
        array_push( $arrayMeta,(object)[
          'documentoMetadatoId' => $metadato->documentoMetadatoId,
          'descripcion' => $metadato->descripcion
        ]);
      }

      foreach($resUbicacion as $ubicacion){
        array_push( $arrayUbicacion,(object)[
          'documentoUbicacionId' => $ubicacion->documentoUbicacionId,
          'ubicacionId' => $ubicacion->ubicacionId
        ]);
      }
      array_push( $arrayArchivos,(object)[
          'id'=> $dir->documentodId,
          'documentoId'=> $dir->documentoId,
          'documentoPadre'=> $dir->documentoPadre,
          'tipoDocid'=>$dir->tipoDocumentoId,
          'topicoid'=> $dir->topicoID,
          'privacidadid'=> $dir->privacidadDocumentoId,
          'privacidadText' => $dir->descripcion,
          'areaId'=> $dir->areaId,
          'moduloId'=> $dir->moduloId,
          'titulo'=> $dir->titulo,
          'archivo'=> $dir->archivo,
          'urlArchivo'=> $file,
          'extension'=> $dir->extension,
          'size'=> $this->formatBytes($dir->tamanio),
          'numeroConsultas'=> $dir->numeroConsultas,
          'numeroDescargas' => $dir->numeroDescargas,
          'fechaCaptura' => $dir->fechaCaptura,
          'fechaUltimaConsulta' => $dir->fechaUltimaConsulta,
          'fechaUltimaDescarga' => $dir->fechaUltimaDescarga,
          'icono' => $icono,
          'icono_name'=> $dir->iconoDocumento,
          'metadatos'=> $arrayMeta,
          'ubicacion' => $arrayUbicacion
        ]);

        $arrayMeta=[];
        $arrayUbicacion=[];
        }

          array_push( $myArray,(object)[
            'carpetas'=> $arrayCarpetas,
            'archivos'=> $arrayArchivos
             ]);

          return response()->json($myArray, 200);;
        }else{
          return response()->json(null, 500); 
  
      }
    }

    protected function BuscadorPublico(Request $request){

        $ubicacion= Ubicacion::firstWhere('descripcion',$request["ubicacion"]);
        $query= $request["query"];
        $tipoDoc= $request["tipoDoc"];
        $Topico= $request["topico"];
        $Area= $request["area"];
        $Modulo= $request["modulo"];
        $sql="";
        $arrayMeta=[];
        $arrayUbicacion=[];
        $script= "select distinct
        a.documentoid,a.titulo,b.documentodId, b.archivo, b.tamanio, b.extension,b.numeroConsultas,b.numeroDescargas, b.fechaCaptura,
        b.fechaUltimaConsulta, b.fechaUltimaDescarga, b.fechaUltimaActualizacion, b.iconoDocumento,
        e.tipoDocumentoId as TipoDocumentoId, f.topicoId as topicoId, g.moduloId as moduloId, h.areaId as AreaId
         from documento a
         left join documentod b on a.documentoid = b.documentoid
        left join documento_metadato c on b.documentoDid = c.documentoDid
        left join documento_ubicacion d on b.documentoDid = d.documentoDid
        left join cat_ubicacion dd on d.ubicacionId = dd.ubicacionId
        left join cat_tipo_documento e on b.tipoDocumentoId = e.tipoDocumentoId
        left join cat_topico f on b.topicoId = f.topicoId
        left join cat_modulo g on b.moduloId = g.moduloId
        left join cat_area h on b.areaId = h.areaId
        where (a.esCarpeta = 0 and a.borrado = 0 and b.borrado = 0) and
        (a.titulo like '%$query%' or b.archivo like '%$query%' or  (c.descripcion like '%$query%' and  c.borrado = 0 )) and
        ( dd.descripcion = '{$request['ubicacion']}' )	and (b.privacidadDocumentoId=3)";

        //Si los campos de busqueda son vacios
        if(empty($tipoDoc) && empty($Topico) && empty($Area) && empty($Modulo)){

        $sql=DB::select($script);

        }
        //si tipo doc no es vacio y demas si
        else if(!empty($tipoDoc) && empty($Topico) && empty($Area) && empty($Modulo)){
          $sql=DB::select($script.' and( e.tipoDocumentoId = '.$tipoDoc.')	');
        }
        //si topico no es vacio los demas si
        else if(empty($tipoDoc) && !empty($Topico) && empty($Area) && empty($Modulo)){
          $sql=DB::select($script.'	and (f.topicoId = '.$Topico.')');
        }
        //si Area no es vacio
        else if(empty($tipoDoc) && empty($Topico) && !empty($Area) && empty($Modulo)){
          $sql=DB::select($script.'	and (h.areaId = '.$Area.')');
        }

        else if(empty($tipoDoc) && empty($Topico) && empty($Area) && !empty($Modulo)){
          $sql=DB::select($script.'	and (g.moduloId = '.$Modulo.')');
        }
        // si tipo doc y topico no son vacios
        else if(!empty($tipoDoc) && !empty($Topico) && empty($Area) && empty($Modulo)){
          $sql=DB::select($script.'	and( e.tipoDocumentoId = '.$tipoDoc.')and (f.topicoId = '.$Topico.')');
        }
        //si Area y Modulo no son vacios
        else if(empty($tipoDoc) && empty($Topico) && !empty($Area) && !empty($Modulo)){
          $sql=DB::select($script.'	and (h.areaId = '.$Area.') and (g.moduloId = '.$Modulo.')');
        }
      //si tipo documento y area no son vacios
      else if(!empty($tipoDoc) && empty($Topico) && !empty($Area) && empty($Modulo)){
        $sql=DB::select($script.'and( e.tipoDocumentoId = '.$tipoDoc.')	and (h.areaId = '.$Area.')');
      }
      else if(!empty($tipoDoc) && !empty($Topico) && !empty($Area) && !empty($Modulo)){
          $sql=DB::select($script.' and( e.tipoDocumentoId = '.$tipoDoc.') and (f.topicoId = '.$Topico.') and (h.areaId = '.$Area.')and (g.moduloId = '.$Modulo.')');
        }
        $myArray=[];

        foreach($sql as $dir){
          $filename=$dir->iconoDocumento;

          $icono=asset('storage/Publico/iconos/'. $filename);
          $header = [
              'Content-Type' => 'image/jpg',
          ];
          $filen=$dir->archivo;
          $file=asset('storage/Publico/'. $filen);

          Documentod::where('documentodId', $dir->documentodId)->increment('numeroBusquedas', 1);

             //Array metadatos
             $resMetadato=DB::select('select * from documento_metadato where documentodId='.$dir->documentodId.' and borrado=0;');
             $resUbicacion=DB::select('select * from documento_ubicacion where documentodId='.$dir->documentodId.' and borrado=0;');

             foreach($resMetadato as $metadato){
               array_push( $arrayMeta,(object)[
                 'documentoMetadatoId' => $metadato->documentoMetadatoId,
                 'descripcion' => $metadato->descripcion
               ]);
             }

             foreach($resUbicacion as $ubicacion){
               array_push( $arrayUbicacion,(object)[
                 'documentoUbicacionId' => $ubicacion->documentoUbicacionId,
                 'ubicacionId' => $ubicacion->ubicacionId
               ]);
             }
        array_push( $myArray,(object)[

          'id'=> $dir->documentodId,
          'documentoId'=> $dir->documentoid,
          // 'documentoPadre'=> $dir->documentoPadre,
          'tipoDocid'=>$dir->TipoDocumentoId,
          'topicoid'=> $dir->topicoId,
          // 'privacidadid'=> $dir->privacidadDocumentoId,
          'areaId'=> $dir->AreaId,
          'moduloId'=> $dir->moduloId,
          'titulo'=> $dir->titulo,
          'archivo'=> $dir->archivo,
          'urlArchivo'=> $file,
          'extension'=> $dir->extension,
          'size'=> $this->formatBytes($dir->tamanio),
          'numeroConsultas'=> $dir->numeroConsultas,
          'numeroDescargas' => $dir->numeroDescargas,
          'fechaCaptura' => $dir->fechaCaptura,
          'fechaUltimaConsulta' => $dir->fechaUltimaConsulta,
          'fechaUltimaDescarga' => $dir->fechaUltimaDescarga,
          'icono' => $icono,
          'metadatos'=> $arrayMeta,
          'ubicacion' => $arrayUbicacion
        ]);

        $arrayMeta=[];
        $arrayUbicacion=[];
        }
        return response()->json($myArray, 200);;
      }


      protected function getArchivosMasDescargados(Request $request){

        $ubicacion= Ubicacion::firstWhere('descripcion',$request["ubicacion"]);

        $admin = Auth::user()->roles;

        if($admin==1){
          $script='Select * from documento as a inner join documentod as b on a.documentoid=b.documentoId LEFT JOIN cat_privacidad_documento as c on b.privacidadDocumentoId= c.privacidadDocumentoId left join
          documento_ubicacion as d on b.documentodId=d.documentodId
          where a.borrado=0 and b.borrado=0  and d.ubicacionId='.$ubicacion->ubicacionId.' and b.numeroDescargas!=0 order by b.numeroDescargas desc limit 16';
        }else{
          $script='Select * from documento as a inner join documentod as b on a.documentoid=b.documentoId LEFT JOIN cat_privacidad_documento as c on b.privacidadDocumentoId= c.privacidadDocumentoId left join
          documento_ubicacion as d on b.documentodId=d.documentodId
          where a.borrado=0 and b.borrado=0 and b.privacidadDocumentoId=3 and d.ubicacionId='.$ubicacion->ubicacionId.' and b.numeroDescargas!=0 order by b.numeroDescargas desc limit 16';
        }
        $sql=DB::select($script);

        $myArray = [];
        $arrayArchivos=[];
        $arrayMeta=[];
        $arrayUbicacion=[];

        foreach($sql as $dir){
        $filename=$dir->iconoDocumento;

        $icono=asset('storage/Publico/iconos/'. $filename);


        $header = [
            'Content-Type' => 'image/jpg',
        ];
        $filen=$dir->archivo;


        $file=asset('storage/Publico/'. $filen);
        array_push( $arrayArchivos,(object)[
            'id'=> $dir->documentodId,
            'documentoId'=> $dir->documentoId,
            'documentoPadre'=> $dir->documentoPadre,
            'tipoDocid'=>$dir->tipoDocumentoId,
            'topicoid'=> $dir->topicoID,
            'privacidadid'=> $dir->privacidadDocumentoId,
            'areaId'=> $dir->areaId,
            'moduloId'=> $dir->moduloId,
            'titulo'=> $dir->titulo,
            'archivo'=> $dir->archivo,
            'urlArchivo'=> $file,
            'extension'=> $dir->extension,
            'size'=> $this->formatBytes($dir->tamanio),
            'numeroConsultas'=> $dir->numeroConsultas,
            'numeroDescargas' => $dir->numeroDescargas,
			 'numeroBusquedas' => $dir->numeroBusquedas,
            'fechaCaptura' => $dir->fechaCaptura,
            'fechaUltimaConsulta' => $dir->fechaUltimaConsulta,
            'fechaUltimaDescarga' => $dir->fechaUltimaDescarga,
            'icono' => $icono,
            'icono_name'=> $dir->iconoDocumento,
            'metadatos'=> $arrayMeta,
            'ubicacion' => $arrayUbicacion
          ]);

          }



            return response()->json($arrayArchivos, 200);;

      }
      protected function getArchivosMasVisitados(Request $request){

        $ubicacion= Ubicacion::firstWhere('descripcion',$request["ubicacion"]);

        $admin = Auth::user()->roles;

        if($admin==1){
          $script='Select * from documento as a inner join documentod as b on a.documentoid=b.documentoId LEFT JOIN cat_privacidad_documento as c on b.privacidadDocumentoId= c.privacidadDocumentoId left join
          documento_ubicacion as d on b.documentodId=d.documentodId
          where a.borrado=0 and b.borrado=0  and d.ubicacionId='.$ubicacion->ubicacionId.' and  b.numeroConsultas!=0 order by b.numeroConsultas desc limit 16';
        }else{
          $script='Select * from documento as a inner join documentod as b on a.documentoid=b.documentoId LEFT JOIN cat_privacidad_documento as c on b.privacidadDocumentoId= c.privacidadDocumentoId left join
          documento_ubicacion as d on b.documentodId=d.documentodId
          where a.borrado=0 and b.borrado=0 and b.privacidadDocumentoId=3 and d.ubicacionId='.$ubicacion->ubicacionId.' and  b.numeroConsultas!=0 order by b.numeroConsultas desc limit 16';
        }
        $sql=DB::select($script);


        $myArray = [];
        $arrayArchivos=[];
        $arrayMeta=[];
        $arrayUbicacion=[];

        foreach($sql as $dir){
        $filename=$dir->iconoDocumento;

        $icono=asset('storage/Publico/iconos/'. $filename);


        $header = [
            'Content-Type' => 'image/jpg',
        ];
        $filen=$dir->archivo;


        $file=asset('storage/Publico/'. $filen);
        array_push( $arrayArchivos,(object)[
            'id'=> $dir->documentodId,
            'documentoId'=> $dir->documentoId,
            'documentoPadre'=> $dir->documentoPadre,
            'tipoDocid'=>$dir->tipoDocumentoId,
            'topicoid'=> $dir->topicoID,
            'privacidadid'=> $dir->privacidadDocumentoId,
            'areaId'=> $dir->areaId,
            'moduloId'=> $dir->moduloId,
            'titulo'=> $dir->titulo,
            'archivo'=> $dir->archivo,
            'urlArchivo'=> $file,
            'extension'=> $dir->extension,
            'size'=> $this->formatBytes($dir->tamanio),
            'numeroConsultas'=> $dir->numeroConsultas,
            'numeroDescargas' => $dir->numeroDescargas,
			 'numeroBusquedas' => $dir->numeroBusquedas,
            'fechaCaptura' => $dir->fechaCaptura,
            'fechaUltimaConsulta' => $dir->fechaUltimaConsulta,
            'fechaUltimaDescarga' => $dir->fechaUltimaDescarga,
            'icono' => $icono,
            'icono_name'=> $dir->iconoDocumento,
            'metadatos'=> $arrayMeta,
            'ubicacion' => $arrayUbicacion
          ]);

          }



            return response()->json($arrayArchivos, 200);;

      }
      protected function getArchivosMasFavoritos(Request $request){

        $ubicacion= Ubicacion::firstWhere('descripcion',$request["ubicacion"]);

        $admin = Auth::user()->roles;

        if($admin==1){
          $script='Select * from documento as a inner join documentod as b on a.documentoid=b.documentoId LEFT JOIN cat_privacidad_documento as c on b.privacidadDocumentoId= c.privacidadDocumentoId left join
          documento_ubicacion as d on b.documentodId=d.documentodId
          where a.borrado=0 and b.borrado=0 and d.ubicacionId='.$ubicacion->ubicacionId.' and b.numeroFavorito!=0 order by b.numeroFavorito desc limit 16';
        }else{
          $script='Select * from documento as a inner join documentod as b on a.documentoid=b.documentoId LEFT JOIN cat_privacidad_documento as c on b.privacidadDocumentoId= c.privacidadDocumentoId left join
          documento_ubicacion as d on b.documentodId=d.documentodId
          where a.borrado=0 and b.borrado=0 and b.privacidadDocumentoId=3 and d.ubicacionId='.$ubicacion->ubicacionId.' and  b.numeroFavorito!=0 order by b.numeroFavorito desc limit 16';
        }
        $sql=DB::select($script);

        $myArray = [];
        $arrayArchivos=[];
        $arrayMeta=[];
        $arrayUbicacion=[];

        foreach($sql as $dir){
        $filename=$dir->iconoDocumento;

        $icono=asset('storage/Publico/iconos/'. $filename);


        $header = [
            'Content-Type' => 'image/jpg',
        ];
        $filen=$dir->archivo;


       $file=asset('storage/Publico/'. $filen);
        array_push( $arrayArchivos,(object)[
            'id'=> $dir->documentodId,
            'documentoId'=> $dir->documentoId,
            'documentoPadre'=> $dir->documentoPadre,
            'tipoDocid'=>$dir->tipoDocumentoId,
            'topicoid'=> $dir->topicoID,
            'privacidadid'=> $dir->privacidadDocumentoId,
            'areaId'=> $dir->areaId,
            'moduloId'=> $dir->moduloId,
            'titulo'=> $dir->titulo,
            'archivo'=> $dir->archivo,
            'urlArchivo'=> $file,
            'extension'=> $dir->extension,
            'size'=> $this->formatBytes($dir->tamanio),
            'numeroConsultas'=> $dir->numeroConsultas,
            'numeroDescargas' => $dir->numeroDescargas,
			 'numeroBusquedas' => $dir->numeroBusquedas,
            'fechaCaptura' => $dir->fechaCaptura,
            'fechaUltimaConsulta' => $dir->fechaUltimaConsulta,
            'fechaUltimaDescarga' => $dir->fechaUltimaDescarga,
            'icono' => $icono,
            'icono_name'=> $dir->iconoDocumento,
            'metadatos'=> $arrayMeta,
            'ubicacion' => $arrayUbicacion
          ]);

          }



            return response()->json($arrayArchivos, 200);;

      }
      protected function getArchivosMasBuscados(Request $request){

        $ubicacion= Ubicacion::firstWhere('descripcion',$request["ubicacion"]);

        $admin = Auth::user()->roles;

        if($admin==1){
          $script='Select * from documento as a inner join documentod as b on a.documentoid=b.documentoId LEFT JOIN cat_privacidad_documento as c on b.privacidadDocumentoId= c.privacidadDocumentoId left join
          documento_ubicacion as d on b.documentodId=d.documentodId
          where a.borrado=0 and b.borrado=0 and d.ubicacionId='.$ubicacion->ubicacionId.' and  b.numeroBusquedas!=0 order by b.numeroBusquedas desc limit 16';
        }else{
          $script='Select * from documento as a inner join documentod as b on a.documentoid=b.documentoId LEFT JOIN cat_privacidad_documento as c on b.privacidadDocumentoId= c.privacidadDocumentoId left join
          documento_ubicacion as d on b.documentodId=d.documentodId
          where a.borrado=0 and b.borrado=0 and b.privacidadDocumentoId=3 and d.ubicacionId='.$ubicacion->ubicacionId.' and  b.numeroBusquedas!=0 order by b.numeroBusquedas desc limit 16';
        }
        $sql=DB::select($script);


        $myArray = [];
        $arrayArchivos=[];
        $arrayMeta=[];
        $arrayUbicacion=[];

        foreach($sql as $dir){
        $filename=$dir->iconoDocumento;

        $icono=asset('storage/Publico/iconos/'. $filename);


        $header = [
            'Content-Type' => 'image/jpg',
        ];
        $filen=$dir->archivo;


        $file=asset('storage/Publico/'. $filen);
        array_push( $arrayArchivos,(object)[
            'id'=> $dir->documentodId,
            'documentoId'=> $dir->documentoId,
            'documentoPadre'=> $dir->documentoPadre,
            'tipoDocid'=>$dir->tipoDocumentoId,
            'topicoid'=> $dir->topicoID,
            'privacidadid'=> $dir->privacidadDocumentoId,
            'areaId'=> $dir->areaId,
            'moduloId'=> $dir->moduloId,
            'titulo'=> $dir->titulo,
            'archivo'=> $dir->archivo,
            'urlArchivo'=> $file,
            'extension'=> $dir->extension,
            'size'=> $this->formatBytes($dir->tamanio),
            'numeroConsultas'=> $dir->numeroConsultas,
            'numeroDescargas' => $dir->numeroDescargas,
			 'numeroBusquedas' => $dir->numeroBusquedas,
            'fechaCaptura' => $dir->fechaCaptura,
            'fechaUltimaConsulta' => $dir->fechaUltimaConsulta,
            'fechaUltimaDescarga' => $dir->fechaUltimaDescarga,
            'icono' => $icono,
            'icono_name'=> $dir->iconoDocumento,
            'metadatos'=> $arrayMeta,
            'ubicacion' => $arrayUbicacion
          ]);

          }



            return response()->json($arrayArchivos, 200);;

      }
    function formatBytes($size, $precision = 2)
    {
    $base = log($size, 1024);
    $suffixes = array('', 'KB', 'MB', 'GB', 'TB');

    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }
}
