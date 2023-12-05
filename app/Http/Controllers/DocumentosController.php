<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\CatTopico as Topico;
use App\Models\CatPrivacidadDocumento as Privacidad;
use App\Models\CatTipoDocumento as TipoDoc;
use App\Models\Documentod as Documentod;
use App\Models\CatArea as Area;
use App\Models\CatModulo as Modulo;
use App\Models\CatUbicacion as Ubicacion;
use App\Models\CatUbicacion;

class DocumentosController extends Controller
{

  protected function getCmbArchivos(){

    if(Auth::check()){

      $arrCatPrivacidad=[];
      $arrCatTipoDoc=[];
      $arrCatArea=[];
      $arrCatModulo=[];
      $arrCatUbicacion=[];
      $arrCatTopico=[];
      $myArray=[];

        $res2=  DB::table('cat_privacidad_documento')
        ->where('borrado', '0')->get();

        $res3=  DB::table('cat_tipo_documento')
        ->where('borrado', '0')->get();

        $res4=  DB::table('cat_area')
        ->where('borrado', '0')->get();

        $res5=  DB::table('cat_modulo')
        ->where('borrado', '0')->get();

        $res6=  DB::table('cat_ubicacion')
        ->where('borrado', '0')->get();

        $res7 =  DB::table('cat_topico')
        ->where('borrado', '0')->get();

        array_push($myArray,(object)[
          'CatPrivacidad'=> $res2,
          'CatTipoDoc'=> $res3,
          'CatArea'=> $res4,
          'CatModulo'=> $res5,
          'CatUbicacion'=> $res6,
          'CatTopico'=> $res7,

        ]);

    return response()->json($myArray, 200);
}else{

    return response()->json(null);
  }
}
    protected function getCatPrivacidadDocumento(){

        if(Auth::check()){
        $res=  DB::table('cat_privacidad_documento');


        return response()->json($res->get(), 200);
    }else{

        return response()->json(null);
      }
    }

    protected function addPrivacidad(Request $request){

      $datos= $this->validate(request(),[

        'descripcion' => 'required',
        'borrado' => 'required',
        ]);

      if(Auth::check()){

          try {
            $privacidad= new Privacidad();

         $privacidad->descripcion= $datos["descripcion"];
         $privacidad->borrado= $datos["borrado"];
         $privacidad->save();
          } catch (\Throwable $th) {
            return response()->json(['error'=>'Error al insertar.'],500);
          }


         return response()->json(['success'=>'Privacidad agregado Correctamente.'],200);
  }else{

    return response()->json(['error'=>'No tienes Sesion'],200);
    }
  }

    protected function updatePrivacidad(Request $request){

      $datos= $this->validate(request(),[

        'privacidadDocumentoId' => 'required',
        'descripcion' => 'required',
        'borrado' => 'required',
        ]);

      if(Auth::check()){

          try {
            $privacidad= Privacidad::find($datos["privacidadDocumentoId"]);

         $privacidad->descripcion= $datos["descripcion"];
         $privacidad->borrado= $datos["borrado"];
         $privacidad->save();
          } catch (\Throwable $th) {
            return response()->json(['error'=>'Error al modificar.'],500);
          }


         return response()->json(['success'=>'Privacidad Modificado Correctamente.'],200);
  }else{

    return response()->json(['error'=>'No tienes Sesion'],200);
    }
  }
    protected function getCatTipoDocumento(){

        if(Auth::check()){
        $res=  DB::table('cat_tipo_documento');


        return response()->json($res->get(), 200);
    }else{

        return response()->json(null);
      }
    }
    protected function addTipoDoc(Request $request){

      $datos= $this->validate(request(),[

        'nombreTipoDocumento' => 'required',
        'borrado' => 'required',
        'esSistema' => 'required',
        ]);

      if(Auth::check()){

          try {
            $tipodoc= new TipoDoc();

            $tipodoc->nombreTipoDocumento= $datos["nombreTipoDocumento"];
            $tipodoc->borrado= $datos["borrado"];
            $tipodoc->esDeSistema= $datos["esSistema"];
            $tipodoc->save();
          } catch (\Throwable $th) {
            return response()->json(['error'=>'Error al insertar.-> '.$th],500);
          }


         return response()->json(['success'=>'TipoDoc agregado Correctamente.'],200);
  }else{

    return response()->json(['error'=>'No tienes Sesion'],200);
    }
  }

    protected function updateTipoDoc(Request $request){

      $datos= $this->validate(request(),[

        'tipoDocumentoId' => 'required',
        'nombreTipoDocumento' => 'required',
        'borrado' => 'required',
        'esSistema' => 'required',
        ]);

      if(Auth::check()){

          try {
            $tipodoc= TipoDoc::find($datos["tipoDocumentoId"]);

            $tipodoc->nombreTipoDocumento= $datos["nombreTipoDocumento"];
            $tipodoc->esDeSistema= $datos["esSistema"];
            $tipodoc->borrado= $datos["borrado"];
            $tipodoc->save();
          } catch (\Throwable $th) {
            return response()->json(['error'=>'Error al modificar.'],500);
          }


         return response()->json(['success'=>'Tipo Doc Modificado Correctamente.'],200);
  }else{

    return response()->json(['error'=>'No tienes Sesion'],200);
    }
  }

    protected function getCatArea(){

        if(Auth::check()){
        $res=  DB::table('cat_area')
        ->where('borrado', '0');

        return response()->json($res->get(), 200);
    }else{

        return response()->json(null);
      }
    }

    protected function addArea(Request $request){

      $datos= $this->validate(request(),[

        'descripcion' => 'required',
        'borrado' => 'required',
        ]);

      if(Auth::check()){

          try {
            $area= new Area();

         $area->descripcion= $datos["descripcion"];
         $area->borrado= $datos["borrado"];
         $area->save();
          } catch (\Throwable $th) {
            return response()->json(['error'=>'Error al insertar.'],500);
          }


         return response()->json(['success'=>'Area agregado Correctamente.'],200);
  }else{

    return response()->json(['error'=>'No tienes Sesion'],200);
    }
  }

    protected function updateArea(Request $request){

      $datos= $this->validate(request(),[

        'areaId' => 'required',
        'descripcion' => 'required',
        'borrado' => 'required',
        ]);

      if(Auth::check()){

          try {
            $area= Area::find($datos["areaId"]);

         $area->descripcion= $datos["descripcion"];
         $area->borrado= $datos["borrado"];
         $area->save();
          } catch (\Throwable $th) {
            return response()->json(['error'=>'Error al modificar.'],500);
          }


         return response()->json(['success'=>'Area Modificado Correctamente.'],200);
  }else{

    return response()->json(['error'=>'No tienes Sesion'],200);
    }
  }



    protected function getCatModulo(){

        if(Auth::check()){
        $res=  DB::table('cat_modulo');


        return response()->json($res->get(), 200);
    }else{

        return response()->json(null);
      }
    }

    protected function addModulo(Request $request){

      $datos= $this->validate(request(),[

        'descripcion' => 'required',
        'borrado' => 'required',
        ]);

      if(Auth::check()){

          try {
            $modulo= new Modulo();

         $modulo->descripcion= $datos["descripcion"];
         $modulo->borrado= $datos["borrado"];
         $modulo->save();

          } catch (\Throwable $th) {
            return response()->json(['error'=>'Error al insertar.'],500);
          }


         return response()->json(['success'=>' Modulo agregado Correctamente.'],200);
  }else{

    return response()->json(['error'=>'No tienes Sesion'],200);
    }
  }

    protected function updateModulo(Request $request){

      $datos= $this->validate(request(),[

        'moduloId' => 'required',
        'descripcion' => 'required',
        'borrado' => 'required',
        ]);

      if(Auth::check()){

        try {
            $modulo= Modulo::find($datos["moduloId"]);

          $modulo->descripcion= $datos["descripcion"];
          $modulo->borrado= $datos["borrado"];
          $modulo->save();

          } catch (\Throwable $th) {
            return response()->json(['error'=>'Error al modificar.'],500);
          }


         return response()->json(['success'=>'Modulo Modificado Correctamente.'],200);
  }else{

    return response()->json(['error'=>'No tienes Sesion'],200);
    }
  }


    protected function getCatUbicacion(){

        if(Auth::check()){
        $res =  DB::table('cat_ubicacion')
         ->where('borrado', '0');

        return response()->json($res->get(), 200);
    }else{

        return response()->json(null);
      }
    }

    protected function getCatTopico(){

      if(Auth::check()){
      $res =  DB::table('cat_topico');

      return response()->json($res->get(), 200);
  }else{

      return response()->json(null);
    }
  }
  protected function addTopico(Request $request){

    $datos= $this->validate(request(),[

      'descripcion' => 'required',
      'borrado' => 'required',
      ]);

    if(Auth::check()){

        try {
          $topico= new Topico();

       $topico->descripcion= $datos["descripcion"];
       $topico->borrado= $datos["borrado"];
       $topico->save();
        } catch (\Throwable $th) {
          return response()->json(['error'=>'Error al insertar.'],500);
        }


       return response()->json(['success'=>'Topico agregado Correctamente.'],200);
}else{

  return response()->json(['error'=>'No tienes Sesion'],200);
  }
}

  protected function updateTopico(Request $request){

    $datos= $this->validate(request(),[

      'topicoId' => 'required',
      'descripcion' => 'required',
      'borrado' => 'required',
      ]);

    if(Auth::check()){

        try {
          $topico= Topico::find($datos["topicoId"]);

       $topico->descripcion= $datos["descripcion"];
       $topico->borrado= $datos["borrado"];
       $topico->save();
        } catch (\Throwable $th) {
          return response()->json(['error'=>'Error al modificar.'],500);
        }


       return response()->json(['success'=>'Topico Modificado Correctamente.'],200);
}else{

  return response()->json(['error'=>'No tienes Sesion'],200);
  }
}

  protected function getCatDocPadre(){

    if(Auth::check()){
    $res =  DB::table('documento')
     ->where('borrado', '0')
     ->where('esCarpeta','1');

    return response()->json($res->get(), 200);
}else{

    return response()->json(null);
  }
}

protected function ControlArchivosPrivacidad(Request $req){
  if($req["privacidadId"]){
  return  $this->getCarpetas($req["privacidadId"]);
  }else{
    $privacidadId=3;
  return  $this->getCarpetas($privacidadId);
    // $script='Select * from documento inner join documentod on documento.documentoid=documentod.documentoId where documento.documentoPadre=0 and documento.borrado=0 and documentod.borrado=0 and documentod.privacidadDocumentoId=5;';
  }

}
    protected function getCarpetas($privacidadId){
      if(Auth::check()){
      //get Id User
      $id = Auth::user()->usuarioId;
      $admin = Auth::user()->roles;

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
if($admin==1){
  $script='Select * from documento inner join documentod on documento.documentoid=documentod.documentoId
      LEFT JOIN cat_privacidad_documento  on documentod.privacidadDocumentoId= cat_privacidad_documento.privacidadDocumentoId
       where documento.documentoPadre=0 and documento.borrado=0 and documentod.borrado=0 and documentod.privacidadDocumentoId='.$privacidadId.'  order by documento.titulo asc;';
  }else{
      $script='Select * from documento inner join documentod on documento.documentoid=documentod.documentoId
      inner join usuariodocumentod on documentod.documentodId= usuariodocumentod.documentodId LEFT JOIN
      cat_privacidad_documento  on documentod.privacidadDocumentoId= cat_privacidad_documento.privacidadDocumentoId
       where documento.documentoPadre=0 and documento.borrado=0 and documentod.borrado=0 and documentod.privacidadDocumentoId='.$privacidadId.' and usuariodocumentod.usuarioId='.$id.' order by documento.titulo asc;';
}
      $sql=DB::select($script);




      foreach($sql as $dir){
          $filename=$dir->iconoDocumento;

          $icono=asset('storage/Publico/iconos/'. $filename);
          $header = [
              'Content-Type' => 'image/jpg',
          ];
          $filen=$dir->archivo;
          $privacidad=$this->getPrivacidad($dir->privacidadDocumentoId);
          $file= Storage::disk($privacidad)->url($filen);
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

      return response()->json(null);
    }
    }


    protected function getSubCarpetas(Request $request){
      if(Auth::check()){
      //get Id User
      $id = Auth::user()->usuarioId;
      $admin = Auth::user()->roles;

      $res=  DB::table('documento')
      ->where('borrado', '0')->where("esCarpeta",'1')->where('documentoPadre','=',$request["documentoPadre"])->orderBy('titulo', 'asc')->get();

      if($admin==1){
        $script='Select * from documento inner join documentod on documento.documentoid=documentod.documentoId
            LEFT JOIN cat_privacidad_documento  on documentod.privacidadDocumentoId= cat_privacidad_documento.privacidadDocumentoId
            where documento.documentoPadre='.$request["documentoPadre"].' and documento.borrado=0 and documentod.borrado=0
            and documentod.privacidadDocumentoId='.$request["privacidadId"].' order by documento.titulo asc';
      }else{

      $script='Select * from documento inner join documentod on documento.documentoid=documentod.documentoId
      inner join usuariodocumentod on documentod.documentodId= usuariodocumentod.documentodId LEFT JOIN
      cat_privacidad_documento  on documentod.privacidadDocumentoId= cat_privacidad_documento.privacidadDocumentoId
       where documento.documentoPadre='.$request["documentoPadre"].' and documento.borrado=0 and documentod.borrado=0
       and documentod.privacidadDocumentoId='.$request["privacidadId"].' and usuariodocumentod.usuarioId='.$id.' order by documento.titulo asc';
      }
      $sql=DB::select($script);
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
      $privacidad=$this->getPrivacidad($dir->privacidadDocumentoId);
      $file= Storage::disk($privacidad)->url($filen);
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

          return response()->json(null);
        }
    }

    protected function getArchivosRecientes(Request $request){
      $ubicacion= CatUbicacion::firstWhere('descripcion',$request["ubicacion"]);

      $sql=DB::select('Select TOP 16 * from documento as a inner join documentod as b on a.documentoid=b.documentoId LEFT JOIN cat_privacidad_documento as c on b.privacidadDocumentoId= c.privacidadDocumentoId left join
      documento_ubicacion as d on b.documentodId=d.documentodId
      where a.borrado=0 and b.borrado=0 and b.privacidadDocumentoId=3 and d.ubicacionId='.$ubicacion->ubicacionId.'  ORDER BY b.documentodId desc');



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
      $privacidad=$this->getPrivacidad($dir->privacidadDocumentoId);
      $file= Storage::disk($privacidad)->url($filen);
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

        $arrayMeta=[];
        $arrayUbicacion=[];
        }

          array_push( $myArray,(object)[
            'archivos'=> $arrayArchivos
             ]);

          return response()->json($myArray, 200);;

    }



    protected function Buscador(Request $request){

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
      b.fechaUltimaConsulta, b.fechaUltimaDescarga, b.fechaUltimaActualizacion, b.iconoDocumento, b.privacidadDocumentoId,
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
      (a.titulo like '%". $query."%' or b.archivo like '%". $query."%'	or  (c.descripcion like '%". $query."%' and  c.borrado = 0 ))	";

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
        $privacidad=$this->getPrivacidad($dir->privacidadDocumentoId);
        $file= Storage::disk($privacidad)->url($filen);


    Documentod::where('documentodId', $dir->documentodId)
    ->increment('numeroBusquedas', 1);

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



    protected function contadoresDocumentos(Request $request){
      $ubicacion= Ubicacion::firstWhere('descripcion',$request["ubicacion"]);
      $myArray=[];
      $res=DB::select('Select SUM(b.numeroDescargas)as numero from  documentod as b  inner join
      documento_ubicacion as d on b.documentodId=d.documentodId
      where b.borrado=0  and d.ubicacionId='.$ubicacion->ubicacionId.'');

      $res2=DB::select('Select SUM(b.numeroConsultas)as numero from  documentod as b  inner join
      documento_ubicacion as d on b.documentodId=d.documentodId
      where b.borrado=0  and d.ubicacionId='.$ubicacion->ubicacionId.'');

      $res3=DB::select('Select SUM(b.numeroBusquedas)as numero from  documentod as b  inner join
      documento_ubicacion as d on b.documentodId=d.documentodId
      where b.borrado=0  and d.ubicacionId='.$ubicacion->ubicacionId.' ');

      $res4=DB::select('Select SUM(b.numeroFavorito)as numero from  documentod as b  inner join
      documento_ubicacion as d on b.documentodId=d.documentodId
      where b.borrado=0  and d.ubicacionId='.$ubicacion->ubicacionId.'');
      array_push($myArray,(object)[
        'Consultas'=> $res2,
        'Descargas'=> $res,
        'Busquedas'=> $res3,
        'Favoritos'=> $res4

      ]);

      return response()->json($myArray, 200);;

    }

    protected function getCatTipoAdjunto(){
      if(Auth::check()){
        $res =  DB::table('cat_tipo_adjunto')
         ->where('borrado', '0')
         ->where('activado','1');

        return response()->json($res->get(), 200);
    }else{

        return response()->json(null);
      }
    }

    function formatBytes($size, $precision = 2)
    {
    $base = log($size, 1024);
    $suffixes = array('', 'KB', 'MB', 'GB', 'TB');

    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }

    protected function getPrivacidad($id){
      $privacidad= Privacidad::find($id);

      return $privacidad->descripcion;
  }
}
