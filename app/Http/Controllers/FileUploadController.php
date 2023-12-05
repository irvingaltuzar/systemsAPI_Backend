<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Validator,Redirect,Response;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\File;
use App\Models\Documentod as Documentod;
use App\Models\Documento as Documento;
use App\Models\Usuariodocumentod as UserDoc;
use App\Models\CatUbicacion as CatUbicacion;
use App\Models\DocumentoUbicacion as DocumentoUbi;
use App\Models\DocumentoMetadato as DocMetadato;
use App\Models\CatPrivacidadDocumento as CatPrivacidad;
use App\Models\File as MFile;
use Carbon\Carbon;
// use App\Models\File;
class FileUploadController extends Controller
{
protected function getPrivacidad($id){
    $privacidad= CatPrivacidad::find($id);

    return $privacidad->descripcion;
}
protected function fileStore(Request $request){

    $datos= $this->validate(request(),[
        'file' => 'required',
        'titulo' => 'required',
        'DocPadre' => 'required',
        'privacidad' => 'required',
        'modulo' => 'required',
        'topico' => 'required',
        'area' => 'required',
        'tipo' => 'required',       
        'ubicacion' => 'required',       
        'metadato' => 'required',
        // 'icono' => 'required'       
        ]);
   
        $arrayUbicacion= explode(",",$datos["ubicacion"]);
        $arrayMetadato= explode(",",$datos["metadato"]);
        
      
        $documentodet= new Documentod();
        $documento= new Documento();
        $UserDoc= new UserDoc();
        $file= new MFile();
        $privacidad= $this->getPrivacidad($datos["privacidad"]);

                $uploaded_file = $request->file('file');
                $file_name = $uploaded_file->getClientOriginalName();
                $sizefile= $uploaded_file->getSize();
                $original_ext = $uploaded_file->getClientOriginalExtension();
                $type = $file->getType($original_ext);

                if($request->file('iconofile')){
                    $icono = $request->file('iconofile');
                    $iconoType = $icono->getClientOriginalExtension();
                    $ico_name = $icono->getClientOriginalName();
                    $file->uploadIcono($icono, $ico_name);
                }else{
                    $ico_name=$request["icono"];
                }
               

                if ($file->uploadFile($privacidad, $uploaded_file, $datos["titulo"], $original_ext)) {
                
                    $documento->documentoPadre =$datos["DocPadre"];
                    $documento->titulo =$datos["titulo"];
                    $documento->esCarpeta= 0;
                    $documento->borrado=0;
                    $documento->save();
                    $lastDocid = DB::getPdo()->lastInsertId();
                    
                    $documentodet->documentoId=  $lastDocid;
                    $documentodet->archivo= $datos["titulo"].".".$original_ext;
                    $documentodet->iconoDocumento=  $ico_name;
                    $documentodet->extension= $original_ext;
                    $documentodet->tamanio= $sizefile;
                    $documentodet->tipoDocumentoId= $datos["tipo"];
                    $documentodet->topicoId= $datos["topico"];
                    $documentodet->areaId= $datos["area"];
                    $documentodet->privacidadDocumentoId= $datos["privacidad"];
                    $documentodet->moduloId= $datos["modulo"];
                    $documentodet->numeroDescargas= 0;
                    $documentodet->numeroConsultas= 0;
                    $documentodet->numeroFavorito= 0;
                    $documentodet->numeroBusquedas= 0;
                    $documentodet->fechaCaptura= date('Y-m-d H:i:s');
                    // $documentodet->fechaUltimaConsulta='0000-00-00 00:00:00';
                    // $documentodet->fechaUltimaDescarga='0000-00-00 00:00:00';
                    // $documentodet->fechaUltimaActualizacion='0000-00-00 00:00:00';
                    $documentodet->borrado= 0;
                    $documentodet->save();
                    $docdetid=DB::getPdo()->lastInsertId();
                
                 
                    for ($i = 0; $i <count($arrayUbicacion); $i++) {
                        $docUbi=new DocumentoUbi();
                        $docUbi->documentodId= $docdetid;
                        $docUbi->ubicacionId= $arrayUbicacion[$i];
                        $docUbi->borrado=0;
                        $docUbi->save();
                     }
                     for ($i = 0; $i <count($arrayMetadato); $i++) {
                        $docMeta= new DocMetadato();
                        $docMeta->documentodId= $docdetid;
                        $docMeta->descripcion= $arrayMetadato[$i];
                        $docMeta->borrado=0;
                        $docMeta->save();
                    
                    }
                    //get Id User
                    $id = Auth::user()->usuarioId;
                    $UserDoc->usuarioId=$id;
                    $UserDoc->documentodId= $docdetid;
                    $UserDoc->save();
                    
                    return response()->json(['success'=>'Archivo subido correctamente.'],200);

                }else{
                    return response()->json(['error'=>'No se pudo insertar Archivo!']);
                }

}

protected function EditarArchivo(Request $request){

    $datos= $this->validate(request(),[
        // 'file' => 'required',
        'titulo' => 'required',
        'DocPadre' => 'required',
        'privacidad' => 'required',
        'modulo' => 'required',
        'topico' => 'required',
        'area' => 'required',
        'tipo' => 'required',       
        'ubicacion' => 'required',       
        'metadato' => 'required',
        'docId' => 'required',      
        'docDetId' => 'required', 
        ]);

       $fullname="";
        $arrayUbicacion= explode(",",$datos["ubicacion"]);
        $arrayMetadato= explode(",",$datos["metadato"]);
        $docdetid=$datos["docDetId"];
        $privacidad= $this->getPrivacidad($datos["privacidad"]);
        $documentodet=  Documentod::find($docdetid);
        $documento= Documento::find($datos["docId"]);
        $file= new MFile();
        try {
            if($request->file('file')){
                $uploaded_file = $request->file('file');
                $file_name = $uploaded_file->getClientOriginalName();
                $sizefile= $uploaded_file->getSize();
                $original_ext = $uploaded_file->getClientOriginalExtension();
                $type = $file->getType($original_ext);
                $fullname=$request["titulo"].'.'. $original_ext;

                $documentodet->extension= $original_ext;
                $documentodet->tamanio= $sizefile;
                if (Storage::disk($privacidad)->exists($file_name)) {

                    unlink("../Storage/app/$privacidad/$file_name");
                    $file->uploadFile($privacidad, $uploaded_file, $datos["titulo"], $original_ext);
                }else{
                    $file->uploadFile($privacidad, $uploaded_file, $datos["titulo"], $original_ext);
                }
                
            }else if($request['file_name'] != ""){
             $orig=$request['file_name'];
             $original_ext=explode(".",$request["file_name"]);
             $fullname= $request["titulo"].'.'.$original_ext[1];
             if($request["file_name"] != $fullname){
                rename("../Storage/app/$privacidad/$orig", "../Storage/app/$privacidad/$fullname");
             }
            }
        } catch (\Throwable $th) {
            throw $th;
        }


                if($request->file('iconofile')){
                    $icono = $request->file('iconofile');
                    $iconoType = $icono->getClientOriginalExtension();
                    $ico_name = $icono->getClientOriginalName();
                    $file->uploadIcono($icono, $ico_name);
                }else{
                    $ico_name=$request["icono"];
                }
               

                // if () {
                //Update documento
                    $documento->titulo =$datos["titulo"];
                    $documento->save();
                //update DocumentoD    
                    $documentodet->archivo= $fullname;
                    $documentodet->iconoDocumento=  $ico_name;
                    $documentodet->tipoDocumentoId= $datos["tipo"];
                    $documentodet->topicoId= $datos["topico"];
                    $documentodet->areaId= $datos["area"];
                    $documentodet->privacidadDocumentoId= $datos["privacidad"];
                    $documentodet->moduloId= $datos["modulo"];
                    $documentodet->fechaUltimaActualizacion= date('Y-m-d H:i:s');
                    $documentodet->update();
                
                    $docUbicacion= DB::table('documento_ubicacion')->where('documentodId',$docdetid)->where('borrado',0)->get();
                        //Agregar nueva ubicacion agregada por usuario
                    for ($i = 0; $i <count($arrayUbicacion); $i++) {
                        $Ubirow= DocumentoUbi::where('documentodId',$docdetid)->where('ubicacionId',$arrayUbicacion[$i])->first();
                        if(!$Ubirow){
                            $docUbi= new DocumentoUbi();
                            $docUbi->documentodId= $docdetid;
                            $docUbi->ubicacionId= $arrayUbicacion[$i];
                            $docUbi->borrado=0;
                            $docUbi->save();
                        }else{
                            $raw =DocumentoUbi::find($Ubirow->documentoUbicacionId);
                            $raw->borrado=0;
                            $raw->save();
                        }
                       
                     }
                     //Borrar ubicacion logica
                     foreach($docUbicacion as $ubicacion){
                        $nUbicacion=$ubicacion->ubicacionId;
                        if(!in_array($nUbicacion, $arrayUbicacion)){
                            $raw= DocumentoUbi::find($ubicacion->documentoUbicacionId);
                            $raw->borrado=1;
                            $raw->save();
                        }
                    }

                    //consulta de todos los elementos actuales de metadatos 
                    $metadatos= DB::table('documento_metadato')->where('documentodId',$docdetid)->where('borrado',0)->get();
                      
                            //Agregar nuevo metadato
                        for ($i = 0; $i <count($arrayMetadato); $i++) {
                               $met= DB::table('documento_metadato')->where('documentodId',$docdetid)->where('descripcion',$arrayMetadato[$i])->first();
                                if(!$met){
                                $docMeta= new DocMetadato();
                                $docMeta->documentodId= $docdetid;
                                $docMeta->descripcion= $arrayMetadato[$i];
                                $docMeta->borrado=0;
                                $docMeta->save();
                                }else{
                                    $raw= DocMetadato::find($met->documentoMetadatoId);
                                    $raw->borrado=0;
                                    $raw->save();

                                }
                            }

                            //Borrar metadato que no exista
                        foreach($metadatos as $meta){
                            $descripcion=$meta->descripcion;
                            if(!in_array($descripcion, $arrayMetadato)){
                                $raw= DocMetadato::find($meta->documentoMetadatoId);
                                $raw->borrado=1;
                                $raw->save();
                            }
                        }
                   
                    return response()->json(['success'=>'Archivo Modificado Correctamente.'],200);

                // }else{
                //     return response()->json(['error'=>'No se pudo insertar Archivo!'],404);
                // }

}
protected function Crear_Carpeta(Request $request){
    //Validacion de datos
    $datos= $this->validate(request(),[
        'titulo' => 'required',
        'DocPadre' => 'required',
        ]);

    $titulo=$datos["titulo"];
    $DocPadre=$datos["DocPadre"];

        //Query Builder tabla Documento
        $res=  DB::table('documento')->insert([
            'documentoPadre' => $DocPadre,
            'titulo' => $titulo,
            'esCarpeta' => 1,
            'borrado' => 0
        ]);

    if($res==true){
        return response()->json(['success'=>'Carpeta Creada Correctamente.']);
    }else{
        return response()->json(['error'=>'Carpeta ya Existe.']);
    }
        
}

protected function EditarCarpeta(Request $request){

    $datos= $this->validate(request(),[
        
        'titulo' => 'required',
        'docId' => 'required'
       
        ]);
        $documento= Documento::find($datos["docId"]);

        $documento->titulo= $datos["titulo"];
        $documento->save();

      return response()->json(['success'=>'Carpeta Modificada Correctamente.'],200);

               

}

public function deleteCarpeta(Request $file){
 


 if(Auth::check()){    
    $datos= $this->validate(request(),[
        
        'docId' => 'required'
       
        ]);
        $sql=DB::table('documento')->where('documentoPadre',$datos["docId"])->where('borrado',0)->count();

        if($sql>0){
            return response()->json(['error'=>'No se puede eliminar la carpeta contiene archivos.'],200);

        }else{


        $documento= Documento::find($datos["docId"]);

        $documento->borrado=1;
        $documento->save();
        return response()->json(['success'=>'Carpeta Eliminada correctamente.'],200);

    }


    }else{

    return response()->json(['error'=>'No tienes sesión.']);
  }
}
protected function subir_archivo(Request $request){
    $datos= $this->validate(request(),[
        'titulo' => 'required',
        'DocPadre' => 'required',
        'Tipo_Doc'=> 'required',
        'Topico'=>'required',
        'Privacidad' =>'required',
        'Area' => 'required',
        'Ubicacion' => 'required',
        'Metadatos' => 'required'
        ]);


}

protected  function downloadFile(Request $file){
    $filename=$file["file"];

    $path = '../storage/app/Publico/'.$file["file"];
    $header = [ 'Content-Type' => 'application/octet-stream'];
    $exists = Storage::disk('Publico')->exists($file["file"]);
    if($exists){
    Documentod::where('archivo', $filename)
    ->increment('numeroDescargas', 1, ['fechaUltimaDescarga' => Carbon::now()]);
        
        return response()->download($path,$filename, $header); 
    }else{
        throw ValidationException::withMessages([
            'Archivo' => ['Archivo no existe en el directorio']
        ]);
    }
   
}

protected  function viewFile(Request $file){
    $filename=$file["documentodId"];

 try {
    
    Documentod::where('documentodId', $filename)
    ->increment('numeroConsultas', 1, ['fechaUltimaConsulta' => Carbon::now()]);

 } catch (\Throwable $th) {
    throw ValidationException::withMessages([
        'Archivo' => ['Error en query']
    ]);
 }
 return response()->json(['success'=>'Todo Correcto.']);


   
 
}

protected  function addFavorito(Request $file){
    $id=$file["documentodId"];

 try {
    
    Documentod::where('documentodId', $id)
    ->increment('numeroFavorito', 1);

 } catch (\Throwable $th) {
    throw ValidationException::withMessages([
        'Archivo' => ['Error en query']
    ]);
 }
 return response()->json(['success'=>'Todo Correcto.']);


   
 
}
public function deleteFile(Request $file){
   

        $documentoid=$file["documentoId"];
        $documentodid=$file["documentodId"];
        Documentod::where('documentodId',  $documentodid)
        ->update(array('borrado' => 1));
        Documento::where('documentoid',  $documentoid)
        ->update(array('borrado' => 1));


     return response()->json(['success'=>'Archivo Eliminado correctamente.']);

  
}

        public function scopeNombreTipoDocumeto($query, $sabor){
            return $query->where('sabor',$sabor);
        }
        
    
}