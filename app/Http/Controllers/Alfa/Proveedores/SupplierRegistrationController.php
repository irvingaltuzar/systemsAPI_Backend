<?php

namespace App\Http\Controllers\Alfa\Proveedores;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DmiabaSupplierRegistration;
use App\Models\PersonalIntelisis;
use App\Models\CatSupplierSpecialty;
use App\Models\DmiabaDocumentsSupplier;
use App\Models\SupplierSpecialty;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\File;
use App\Services\SendEmailServiceSupplier;
use App\Services\IntelisisSenderService;
// use App\Http\Controllers\Procore\Proveedores\VendorsController;
use Illuminate\Support\Promise\Promise;

class SupplierRegistrationController extends Controller
{

    private $sendEmail, $intelisisService, $vendor;

	public function __construct(SendEmailServiceSupplier $sendEmail, IntelisisSenderService $intelisisService)
	{
		$this->sendEmail = $sendEmail;
		$this->intelisisService = $intelisisService;
        // $this->vendor= $vendor;
	}

    protected function ExistRFC(Request $request){
   
            $intelisis_active = $this->intelisisService->supplierStatusRegistration($request["rfc"]);
        $res= DmiabaSupplierRegistration::with(['getDocumentSupplierAll','specialities'])->where("rfc",$request["rfc"])->first();
    if(!$intelisis_active){
        return response()->json(null, 200);

    }
    return response()->json([
        'local' => $res,
        'intelisis' => $intelisis_active,
    ],200);
   
    }
  
    protected function addSupplier(Request $request){
        if(Auth::check()){
        
        $sup= new DmiabaSupplierRegistration();

        $sup->business_name=$request["business_name"];
        $sup->type_person=$request["type_person"];
        $sup->rfc=$request["rfc"];
        $sup->email=$request["email"];
        $sup->phone=$request["phone"];
        $sup->contact=$request["contact"];
        $sup->suburb=$request["suburb"];
        $sup->address=$request["address"];
        $sup->state=$request["state"];
        $sup->country=$request["country"];
        $sup->cp=$request["cp"];
        $sup->city=$request["city"];
        $sup->web_page=$request["web_page"];
        $sup->bank=$request["bank"];
        $sup->bank_clabe=$request["bank_clabe"];
        $sup->bank_account=$request["bank_account"];
        $sup->bank_swift=$request["bank_swift"];
        $sup->credit_days=$request["credit_days"];
        $sup->currency=$request["currency"];
        $sup->classification=$request["classification"];
        $sup->status_files="revision";
        $sup->zip="si";
        $sup->user=auth()->user()->usuario;
        $data["data"]=  $sup;
        $sup->save();
        $lastid=DB::getPdo()->lastInsertId();

        $time= time();
        if($request->file('file_ActaConstitutiva')){
            $uploaded_file = $request->file('file_ActaConstitutiva');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$lastid."_".$time."_file_ActaConstitutiva.".$original_ext;
            
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 1;
                $doc->dmiaba_supplier_registration_id= $lastid;
                $doc->save();
              }
              
        }

        if($request->file('file_INE')){
            $uploaded_file = $request->file('file_INE');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$lastid."_".$time."_file_INE.".$original_ext;
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 2;
                $doc->dmiaba_supplier_registration_id= $lastid;
                $doc->save();
              }
             
        }

        if($request->file('file_ComprobanteDomicilio')){
            $uploaded_file = $request->file('file_ComprobanteDomicilio');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$lastid."_".$time."_file_ComprobanteDomicilio.".$original_ext;
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 3;
                $doc->dmiaba_supplier_registration_id= $lastid;
                $doc->save();
              }
        }
        if($request->file('file_OCOF')){
            $uploaded_file = $request->file('file_OCOF');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$lastid."_".$time."_file_OCOF.".$original_ext;
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 5;
                $doc->dmiaba_supplier_registration_id= $lastid;
                $doc->save();
              }
             
        }
        if($request->file('file_CSF')){
            $uploaded_file = $request->file('file_CSF');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$lastid."_".$time."_file_CSF.".$original_ext;
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 4;
                $doc->dmiaba_supplier_registration_id= $lastid;
                $doc->save();
              }
             
        }
        if($request->file('file_AltaIMSS')){
            $uploaded_file = $request->file('file_AltaIMSS');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$lastid."_".$time."_file_AltaIMSS.".$original_ext;
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 6;
                $doc->dmiaba_supplier_registration_id= $lastid;
                $doc->save();
              }
             
        }
        if($request->file('file_OCOIMSS')){
            $uploaded_file = $request->file('file_OCOIMSS');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$lastid."_".$time."_file_OCOIMSS.".$original_ext;
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 7;
                $doc->dmiaba_supplier_registration_id= $lastid;
                $doc->save();
              }
        }
        if($request->file('file_EstCuenta')){
            $uploaded_file = $request->file('file_EstCuenta');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$lastid."_".$time."_file_EstCuenta.".$original_ext;
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 8;
                $doc->dmiaba_supplier_registration_id= $lastid;
                $doc->save();
              }
        }
        if($request->file('file_CTB')){
            $uploaded_file = $request->file('file_CTB');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$lastid."_".$time."_file_CTB.".$original_ext;
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 9;
                $doc->dmiaba_supplier_registration_id= $lastid;
                $doc->save();
              }
        }
        if($request->file('file_CV')){
            $uploaded_file = $request->file('file_CV');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$lastid."_".$time."_file_CV.".$original_ext;
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 10;
                $doc->dmiaba_supplier_registration_id= $lastid;
                $doc->save();
              }
        }
        if($request->file('file_RFI')){
            $uploaded_file = $request->file('file_RFI');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$lastid."_".$time."_file_RFI.".$original_ext;
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 15;
                $doc->dmiaba_supplier_registration_id= $lastid;
                $doc->save();
              }
        }
        if($request->file('file_REPSE')){
            $uploaded_file = $request->file('file_REPSE');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$lastid."_".$time."_file_REPSE.".$original_ext;
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 16;
                $doc->dmiaba_supplier_registration_id= $lastid;
                $doc->save();
              }
        }
        if($request->file('file_DAER')){
            $uploaded_file = $request->file('file_DAER');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$lastid."_".$time."_file_DAER.".$original_ext;
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 17;
                $doc->dmiaba_supplier_registration_id= $lastid;
                $doc->save();
              }
        }
        if($request->file('file_COM')){
            $uploaded_file = $request->file('file_COM');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$lastid."_".$time."_file_COM.".$original_ext;
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 14;
                $doc->dmiaba_supplier_registration_id= $lastid;
                $doc->save();
              }
        }
        if($request->file('file_COT1')){
            $uploaded_file = $request->file('file_COT1');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$lastid."_".$time."_file_COT1.".$original_ext;
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 13;
                $doc->dmiaba_supplier_registration_id= $lastid;
                $doc->save();
              }
        }
        if($request->file('file_COT2')){
            $uploaded_file = $request->file('file_COT2');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$lastid."_".$time."_file_COT2.".$original_ext;
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 18;
                $doc->dmiaba_supplier_registration_id= $lastid;
                $doc->save();
              }
        }
        if($request->file('file_COT3')){
            $uploaded_file = $request->file('file_COT3');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$lastid."_".$time."_file_COT3.".$original_ext;
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 19;
                $doc->dmiaba_supplier_registration_id= $lastid;
                $doc->save();
              }
        }
        if($request->file('file_COT4')){
            $uploaded_file = $request->file('file_COT4');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$lastid."_".$time."_file_COT4.".$original_ext;
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 20;
                $doc->dmiaba_supplier_registration_id= $lastid;
                $doc->save();
              }
        }
        $data["subject"] = "Notificación: Nuevo proveedor";
        $adm = DB::table("cat_supplier_notification")->where("deleted_at",null)->select('mail')->get();
        $mails=[];
        foreach ($adm as $row) {
            $mails[]= $row->mail;
        }
        $this->sendEmail->newSupplierNotification($data, $mails);

        return response()->json(['success'=>'Se ha creado nuevo proveedor.','id'=> $lastid],200);
        }
        else{
            return response()->json(null, 500);

        }
    }
   
    protected function addSupplierWeb(Request $request){
        // if(Auth::check()){
        if($request["id"] !=""){
           $sup= DmiabaSupplierRegistration::find($request["id"]);
           $sup->update_user= null;

        }else{
            $sup= new DmiabaSupplierRegistration();

        }

        $sup->business_name=$request["business_name"];
        $sup->comercial_name=$request["comercial_name"];
        $sup->type_person=$request["type_person"];
        $sup->rfc=$request["rfc"];
        $sup->email=$request["email"];
        $sup->phone=$request["phone"];
        $sup->contact=$request["contact"];
        $sup->suburb=$request["suburb"];
        $sup->address=$request["address"];
        $sup->state=$request["state"];
        $sup->country=$request["country"];
        $sup->cp=$request["cp"];
        $sup->city=$request["city"];
        $sup->web_page=$request["web_page"];
        $sup->bank=$request["bank"];
        $sup->bank_clabe=$request["bank_clabe"];
        $sup->bank_account=$request["bank_account"];
        $sup->bank_swift=$request["bank_swift"];
        $sup->credit_days=$request["credit_days"];
        $sup->currency=$request["currency"];
        $sup->classification=$request["classification_main"];
        $sup->status_files="revision";
        $sup->status=$request["status"];
        $sup->speciality_main=$request["classification_main"];
        $sup->contact_reference=$request["contact_reference"];
        $sup->zip="si";
        $sup->user= env('USER_PROCORE');
        $data["data"]=  $sup;

        $sup->save();
        if($request["id"]!=""){
            $lastid=$request["id"];
        }else{
            $lastid=DB::getPdo()->lastInsertId();

        }

        $arrayResult = explode(",", $request["classification_aditional"]);

        if (!empty($arrayResult) && array_filter($arrayResult, 'strlen')) {
            foreach ($arrayResult as $specialty) {
                $spe = SupplierSpecialty::updateOrCreate([
                    'supplier_id' =>  $lastid,
                    'cat_supplier_specialty' => $specialty
                ]);
            }
        }

        $time= time();
        for ($i=0; $i < $request["nfiles"]; $i++) { 
            $n="files".$i;
        $file = $request->file($n);
        $originalName = $file->getClientOriginalName();
        // Obtenemos el nombre del archivo sin la extensión
        $fileNameWithoutExtension = pathinfo($originalName, PATHINFO_FILENAME);
        
        // Eliminamos los caracteres especiales del nombre del archivo
        $cleanFileName =preg_replace('/[^A-Za-z0-9\-]/', '_', $fileNameWithoutExtension);
        
        // Obtenemos la extensión del archivo
        $extension = $file->getClientOriginalExtension();
        
        // Concatenamos el nombre limpio con la extensión
        $cleanFileNameWithExtension = $cleanFileName . '.' . $extension;
        
        // Construimos el nuevo nombre de archivo completo
        $file_name = $lastid . '_' . $cleanFileNameWithExtension;
            
        if(Storage::disk("Proveedores")->putFileAs("/", $file, $file_name)){
            $doc= new DmiabaDocumentsSupplier();
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 21;
                $doc->dmiaba_supplier_registration_id= $lastid;
                $doc->save();
            }
        }
  
  
        $data["subject"] = "Notificación: Nuevo proveedor";
        $adm = DB::table("cat_supplier_notification")->where("deleted_at",null)->select('mail')->get();
        $mails=[];
        foreach ($adm as $row) {
            $mails[]= $row->mail;
        }
        if($request["status"]==0){
        $this->sendEmail->newSupplierNotification($data, $mails);
        }
        return response()->json(['success'=>'Se ha creado nuevo proveedor.','id'=> $lastid],200);
   
    }

    protected function DeleteSupplierWeb(Request $request){
        try {
            $supplier =  DmiabaSupplierRegistration::destroy($request["id"]);
            
            return response()->json(['success'=>'Se ha eliminado proveedor.'],200);
            
        } catch (\Throwable $th) {
            throw $th;
        }

    }
    protected function DeleteFilesSupplierWeb(Request $request){
        try {
            $files =  DmiabaDocumentsSupplier::where('id',$request["id"])->delete();
            $name=$request["name"];
            unlink("../Storage/app/Proveedores/$name");
            $supplier= DmiabaDocumentsSupplier::where("dmiaba_supplier_registration_id",$request["supplier_id"])->get();

            return response()->json(['success'=>'Se ha eliminado archivo correctamente.',
                'get_document_supplier_all'=>  $supplier],200);
            
        } catch (\Throwable $th) {
            throw $th;
        }

    }
    protected function updateSupplier(Request $request){
        if(Auth::check()){
        $sup=  DmiabaSupplierRegistration::find($request["id"]);

        $sup->business_name=$request["business_name"];
        $sup->type_person=$request["type_person"];
        $sup->rfc=$request["rfc"];
        $sup->email=$request["email"];
        $sup->phone=$request["phone"];
        $sup->contact=$request["contact"];
        $sup->suburb=$request["suburb"];
        $sup->address=$request["address"];
        $sup->state=$request["state"];
        $sup->country=$request["country"];
        $sup->cp=$request["cp"];
        $sup->city=$request["city"];
        $sup->web_page=$request["web_page"];
        $sup->bank=$request["bank"];
        $sup->bank_clabe=$request["bank_clabe"];
        $sup->bank_account=$request["bank_account"];
        $sup->bank_swift=$request["bank_swift"];
        $sup->credit_days=$request["credit_days"];
        $sup->currency=$request["currency"];
        $sup->classification=$request["classification"];
        $sup->update_user= 0;
        // $sup->status_files="revision";
        // $sup->zip="si";
        $sup->user=auth()->user()->usuario;
        $data["data"]=  $sup;
        $sup->save();
        // $lastid=DB::getPdo()->lastInsertId();

        $time= time();
        if($request->file('file_ActaConstitutiva')){
            $uploaded_file = $request->file('file_ActaConstitutiva');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$request["id"]."_".$time."_file_ActaConstitutiva.".$original_ext;

            $del = DmiabaDocumentsSupplier::where("cat_document_supplier_id",1)->where("dmiaba_supplier_registration_id",$request["id"])->get();
            if(count($del)>0){
                foreach($del as $row) {
                     unlink("../Storage/app/Proveedores/$row->name");
                     $doc= DmiabaDocumentsSupplier::find($row->id);
                     $doc->deleted_at=Carbon::now();
                     $doc->save();   

                }
            }
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 1;
                $doc->dmiaba_supplier_registration_id= $request["id"];
                $doc->save();
              }
              
        }

        if($request->file('file_INE')){
            $uploaded_file = $request->file('file_INE');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$request["id"]."_".$time."_file_INE.".$original_ext;
            $del = DmiabaDocumentsSupplier::where("cat_document_supplier_id",2)->where("dmiaba_supplier_registration_id",$request["id"])->get();
            if(count($del)>0){
                foreach($del as $row) {
                     unlink("../Storage/app/Proveedores/$row->name");
                     $doc= DmiabaDocumentsSupplier::find($row->id);
                     $doc->deleted_at=Carbon::now();
                     $doc->save();   

                }
            }

            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 2;
                $doc->dmiaba_supplier_registration_id= $request["id"];
                $doc->save();
              }
             
        }

        if($request->file('file_ComprobanteDomicilio')){
            $uploaded_file = $request->file('file_ComprobanteDomicilio');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$request["id"]."_".$time."_file_ComprobanteDomicilio.".$original_ext;
            $del = DmiabaDocumentsSupplier::where("cat_document_supplier_id",3)->where("dmiaba_supplier_registration_id",$request["id"])->get();
            if(count($del)>0){
                foreach($del as $row) {
                     unlink("../Storage/app/Proveedores/$row->name");
                     $doc= DmiabaDocumentsSupplier::find($row->id);
                     $doc->deleted_at=Carbon::now();
                     $doc->save();   

                }
            }
            
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 3;
                $doc->dmiaba_supplier_registration_id= $request["id"];
                $doc->save();
              }
        }
        if($request->file('file_OCOF')){
            $uploaded_file = $request->file('file_OCOF');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$request["id"]."_".$time."_file_OCOF.".$original_ext;
            $del = DmiabaDocumentsSupplier::where("cat_document_supplier_id",5)->where("dmiaba_supplier_registration_id",$request["id"])->get();
            if(count($del)>0){
                foreach($del as $row) {
                     unlink("../Storage/app/Proveedores/$row->name");
                     $doc= DmiabaDocumentsSupplier::find($row->id);
                     $doc->deleted_at=Carbon::now();
                     $doc->save();   

                }
            }

            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 5;
                $doc->dmiaba_supplier_registration_id= $request["id"];
                $doc->save();
              }
             
        }
        if($request->file('file_CSF')){
            $uploaded_file = $request->file('file_CSF');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$request["id"]."_".$time."_file_CSF.".$original_ext;
            $del = DmiabaDocumentsSupplier::where("cat_document_supplier_id",4)->where("dmiaba_supplier_registration_id",$request["id"])->get();
            if(count($del)>0){
                foreach($del as $row) {
                     unlink("../Storage/app/Proveedores/$row->name");
                     $doc= DmiabaDocumentsSupplier::find($row->id);
                     $doc->deleted_at=Carbon::now();
                     $doc->save();   

                }
            }
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 4;
                $doc->dmiaba_supplier_registration_id= $request["id"];
                $doc->save();
              }
             
        }
        if($request->file('file_AltaIMSS')){
            $uploaded_file = $request->file('file_AltaIMSS');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$request["id"]."_".$time."_file_AltaIMSS.".$original_ext;
            $del = DmiabaDocumentsSupplier::where("cat_document_supplier_id",6)->where("dmiaba_supplier_registration_id",$request["id"])->get();
            if(count($del)>0){
                foreach($del as $row) {
                     unlink("../Storage/app/Proveedores/$row->name");
                     $doc= DmiabaDocumentsSupplier::find($row->id);
                     $doc->deleted_at=Carbon::now();
                     $doc->save();   

                }
            }
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 6;
                $doc->dmiaba_supplier_registration_id= $request["id"];
                $doc->save();
              }
             
        }
        if($request->file('file_OCOIMSS')){
            $uploaded_file = $request->file('file_OCOIMSS');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$request["id"]."_".$time."_file_OCOIMSS.".$original_ext;
            $del = DmiabaDocumentsSupplier::where("cat_document_supplier_id",7)->where("dmiaba_supplier_registration_id",$request["id"])->get();
            if(count($del)>0){
                foreach($del as $row) {
                     unlink("../Storage/app/Proveedores/$row->name");
                     $doc= DmiabaDocumentsSupplier::find($row->id);
                     $doc->deleted_at=Carbon::now();
                     $doc->save();   

                }
            }
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 7;
                $doc->dmiaba_supplier_registration_id= $request["id"];
                $doc->save();
              }
        }
        if($request->file('file_EstCuenta')){
            $uploaded_file = $request->file('file_EstCuenta');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$request["id"]."_".$time."_file_EstCuenta.".$original_ext;
            $del = DmiabaDocumentsSupplier::where("cat_document_supplier_id",8)->where("dmiaba_supplier_registration_id",$request["id"])->get();
            if(count($del)>0){
                foreach($del as $row) {
                     unlink("../Storage/app/Proveedores/$row->name");
                     $doc= DmiabaDocumentsSupplier::find($row->id);
                     $doc->deleted_at=Carbon::now();
                     $doc->save();   

                }
            }
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 8;
                $doc->dmiaba_supplier_registration_id= $request["id"];
                $doc->save();
              }
        }
        if($request->file('file_CTB')){
            $uploaded_file = $request->file('file_CTB');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$request["id"]."_".$time."_file_CTB.".$original_ext;
            $del = DmiabaDocumentsSupplier::where("cat_document_supplier_id",9)->where("dmiaba_supplier_registration_id",$request["id"])->get();
            if(count($del)>0){
                foreach($del as $row) {
                     unlink("../Storage/app/Proveedores/$row->name");
                     $doc= DmiabaDocumentsSupplier::find($row->id);
                     $doc->deleted_at=Carbon::now();
                     $doc->save();   

                }
            }
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 9;
                $doc->dmiaba_supplier_registration_id= $request["id"];
                $doc->save();
              }
        }
        if($request->file('file_CV')){
            $uploaded_file = $request->file('file_CV');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$request["id"]."_".$time."_file_CV.".$original_ext;
            $del = DmiabaDocumentsSupplier::where("cat_document_supplier_id",10)->where("dmiaba_supplier_registration_id",$request["id"])->get();
            if(count($del)>0){
                foreach($del as $row) {
                     unlink("../Storage/app/Proveedores/$row->name");
                     $doc= DmiabaDocumentsSupplier::find($row->id);
                     $doc->deleted_at=Carbon::now();
                     $doc->save();   

                }
            }
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 10;
                $doc->dmiaba_supplier_registration_id= $request["id"];
                $doc->save();
              }
        }
        if($request->file('file_RFI')){
            $uploaded_file = $request->file('file_RFI');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$request["id"]."_".$time."_file_RFI.".$original_ext;

            $del = DmiabaDocumentsSupplier::where("cat_document_supplier_id",15)->where("dmiaba_supplier_registration_id",$request["id"])->get();
            if(count($del)>0){
                foreach($del as $row) {
                     unlink("../Storage/app/Proveedores/$row->name");
                     $doc= DmiabaDocumentsSupplier::find($row->id);
                     $doc->deleted_at=Carbon::now();
                     $doc->save();   

                }
            }
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 15;
                $doc->dmiaba_supplier_registration_id= $request["id"];
                $doc->save();
              }
        }
        if($request->file('file_REPSE')){
            $uploaded_file = $request->file('file_REPSE');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$request["id"]."_".$time."_file_REPSE.".$original_ext;
            $del = DmiabaDocumentsSupplier::where("cat_document_supplier_id",16)->where("dmiaba_supplier_registration_id",$request["id"])->get();
            if(count($del)>0){
                foreach($del as $row) {
                     unlink("../Storage/app/Proveedores/$row->name");
                     $doc= DmiabaDocumentsSupplier::find($row->id);
                     $doc->deleted_at=Carbon::now();
                     $doc->save();   

                }
            }
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 16;
                $doc->dmiaba_supplier_registration_id= $request["id"];
                $doc->save();
              }
        }
        if($request->file('file_DAER')){
            $uploaded_file = $request->file('file_DAER');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$request["id"]."_".$time."_file_DAER.".$original_ext;
            $del = DmiabaDocumentsSupplier::where("cat_document_supplier_id",17)->where("dmiaba_supplier_registration_id",$request["id"])->get();
            if(count($del)>0){
                foreach($del as $row) {
                     unlink("../Storage/app/Proveedores/$row->name");
                     $doc= DmiabaDocumentsSupplier::find($row->id);
                     $doc->deleted_at=Carbon::now();
                     $doc->save();   

                }
            }
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 17;
                $doc->dmiaba_supplier_registration_id= $request["id"];
                $doc->save();
              }
        }
        if($request->file('file_COM')){
            $uploaded_file = $request->file('file_COM');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$request["id"]."_".$time."_file_COM.".$original_ext;
            $del = DmiabaDocumentsSupplier::where("cat_document_supplier_id",14)->where("dmiaba_supplier_registration_id",$request["id"])->get();
            if(count($del)>0){
                foreach($del as $row) {
                     unlink("../Storage/app/Proveedores/$row->name");
                     $doc= DmiabaDocumentsSupplier::find($row->id);
                     $doc->deleted_at=Carbon::now();
                     $doc->save();   

                }
            }
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 14;
                $doc->dmiaba_supplier_registration_id= $request["id"];
                $doc->save();
              }
        }
        if($request->file('file_COT1')){
            $uploaded_file = $request->file('file_COT1');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$request["id"]."_".$time."_file_COT1.".$original_ext;
            $del = DmiabaDocumentsSupplier::where("cat_document_supplier_id",13)->where("dmiaba_supplier_registration_id",$request["id"])->get();
            if(count($del)>0){
                foreach($del as $row) {
                     unlink("../Storage/app/Proveedores/$row->name");
                     $doc= DmiabaDocumentsSupplier::find($row->id);
                     $doc->deleted_at=Carbon::now();
                     $doc->save();   

                }
            }
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 13;
                $doc->dmiaba_supplier_registration_id= $request["id"];
                $doc->save();
              }
        }
        if($request->file('file_COT2')){
            $uploaded_file = $request->file('file_COT2');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$request["id"]."_".$time."_file_COT2.".$original_ext;
            $del = DmiabaDocumentsSupplier::where("cat_document_supplier_id",18)->where("dmiaba_supplier_registration_id",$request["id"])->get();
            if(count($del)>0){
                foreach($del as $row) {
                     unlink("../Storage/app/Proveedores/$row->name");
                     $doc= DmiabaDocumentsSupplier::find($row->id);
                     $doc->deleted_at=Carbon::now();
                     $doc->save();   

                }
            }
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 18;
                $doc->dmiaba_supplier_registration_id= $request["id"];
                $doc->save();
              }
        }
        if($request->file('file_COT3')){
            $uploaded_file = $request->file('file_COT3');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$request["id"]."_".$time."_file_COT3.".$original_ext;
            $del = DmiabaDocumentsSupplier::where("cat_document_supplier_id",19)->where("dmiaba_supplier_registration_id",$request["id"])->get();
            if(count($del)>0){
                foreach($del as $row) {
                     unlink("../Storage/app/Proveedores/$row->name");
                     $doc= DmiabaDocumentsSupplier::find($row->id);
                     $doc->deleted_at=Carbon::now();
                     $doc->save();   

                }
            }
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 19;
                $doc->dmiaba_supplier_registration_id= $request["id"];
                $doc->save();
              }
        }
        if($request->file('file_COT4')){
            $uploaded_file = $request->file('file_COT4');
            $original_ext = $uploaded_file->getClientOriginalExtension();
            $file_name=$request["id"]."_".$time."_file_COT4.".$original_ext;
            $del = DmiabaDocumentsSupplier::where("cat_document_supplier_id",20)->where("dmiaba_supplier_registration_id",$request["id"])->get();
            if(count($del)>0){
                foreach($del as $row) {
                     unlink("../Storage/app/Proveedores/$row->name");
                     $doc= DmiabaDocumentsSupplier::find($row->id);
                     $doc->deleted_at=Carbon::now();
                     $doc->save();   

                }
            }
            $doc= new DmiabaDocumentsSupplier();
            if( Storage::disk("Proveedores")->putFileAs("/", $uploaded_file, $file_name)){
                $doc->name=$file_name;
                $url= Storage::disk("Proveedores")->url($file_name);
                $doc->url= $url;
                $doc->cat_document_supplier_id= 20;
                $doc->dmiaba_supplier_registration_id= $request["id"];
                $doc->save();
              }
        }

        $data["subject"] = "Notificación: Actualización proveedor";
        $adm = DB::table("cat_supplier_notification")->where("deleted_at",null)->select('mail')->get();
        $mails=[];
        foreach ($adm as $row) {
            $mails[]= $row->mail;
        }
        // $this->sendEmail->EditSupplierNotification($data, $mails);


        return response()->json(['success'=>'Se ha actualizado proveedor.'],200);
    }else{
        return response()->json(null, 500); 

    }
    }
    protected function getMySuppliers(){
        if(Auth::check()){

        $res= DmiabaSupplierRegistration::with('getDocumentSupplierAll')->where("user",auth()->user()->usuario)
        ->orderBy("id","desc")->get();


        return response()->json($res, 200); 
    }else{
        return response()->json(null, 500); 

    }
    }

    protected function getReportAccessSupplier(){
        if(Auth::check()){

        $res= DB::table('seg_subseccion')->join('seg_login', 'seg_subseccion.subsecId', '=', 'seg_login.subsecId')
        ->join('personal_intelisis', 'personal_intelisis.usuario_ad', '=', 'seg_login.loginUsr')
        ->where('seg_subseccion.secId',15)->where('seg_subseccion.public',0)->where("personal_intelisis.status",'ALTA')
        ->orderby('seg_subseccion.subsecDesc')->get();


        return response()->json($res, 200); 
    }else{
        return response()->json(null, 500); 

    }
    }

    protected function getSuppliersEFO(Request $request){
        if(Auth::check()){
            $pagina = $request["pagina"];
            $limite = $request["limite"];
        $res= DmiabaSupplierRegistration::with('getDocumentSupplier')->Paginate( $perPage =  $limite, $columns = ['*'], $pageName = 'page',$page =$pagina);
        // $res= DmiabaSupplierRegistration::with('getDocumentSupplier')->forPage(0, $limite)->get();

        // $collection= collect();
        // $foobar = DmiabaSupplierRegistration::with('getDocumentSupplier')->chunk(1500, function ($rows) use ($collection) {
        //     foreach ($rows as $row) {
        //         $collection->push($row);
        //     }
        // });
        return response()->json($res, 200); 
    }else{
        return response()->json(null, 500); 

    }
    }
    public function fetchSuppliers(Request $request)
	{
        $order_by = isset($request->order_by) ? $request->order_by : 'asc';

        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';

        $search = isset($request->search) ? $request->search : '';


        if(isset($search) && strlen($search) > 0){

			$suppliers = DmiabaSupplierRegistration::with(['files', 'specialities', 'logs.seg_auditorium'])
							->where('business_name', 'LIKE', '%'.$search.'%')
							->orWhere('rfc', 'LIKE', '%'.$search.'%')
							->orWhere('email', 'LIKE', '%'.$search.'%')
							->orWhere('type_person', 'LIKE', '%'.$search.'%')
							->orWhere('contact', 'LIKE', '%'.$search.'%')
							->orWhere('type_supplier', 'LIKE', '%'.$search.'%')
							->orWhere('efo', 'LIKE', '%'.$search.'%')
							->orWhere('date', 'LIKE', '%'.$search.'%')
							->orWhere('status_files', 'LIKE', '%'.$search.'%')
							->orWhere('bank', 'LIKE', '%'.$search.'%')
							->orWhere('bank_account', 'LIKE', '%'.$search.'%')
							->orWhere('bank_clabe', 'LIKE', '%'.$search.'%')
							->orWhere('address', 'LIKE', '%'.$search.'%')
							->orWhere('suburb', 'LIKE', '%'.$search.'%')
							->orWhere('city', 'LIKE', '%'.$search.'%')
							->orWhere('classification', 'LIKE', '%'.$search.'%')
							->orWhereRelation('responsable',function($q) use ($search){
								return $q->orWhere('name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%");
							})
							->orderBy('created_at', 'desc')
							->Paginate(10);
        }else{

            $suppliers = DmiabaSupplierRegistration::with(['files', 'specialities', 'logs.seg_auditorium'])
							->where('status', $request->status)
							->orderBy('created_at',$order_by)
							->Paginate($limit);

        }

        $suppliers->setPath('/suppliers/fetch');

        return $suppliers;
	}
    protected function getCountries(){
        // if(Auth::check()){

        $res= DB::table("countries")->get();

        return response()->json($res, 200);

		// }else{
		// 	return response()->json(null, 500);
		// }
    }

    protected function getStates(){
        // if(Auth::check()){

			$res= DB::table("states")->get();


			return response()->json($res, 200);
		// }else{
		// 	return response()->json(null, 500);

		// }
    }

    protected function getBanks(){
        // if(Auth::check()){

        $res= DB::table("cat_banks_suppliers")->get();


        return response()->json($res, 200); 
    // }else{
    //     return response()->json(null, 500); 

    // }
    }

    protected function getStatesbyCountry(Request $request){
        // if(Auth::check()){

        $res= DB::table("states")->where("country_id",$request["country_id"])->get();

        return response()->json($res, 200);
    // }else{
    //     return response()->json(null, 500);

    // }
    }

    public function getSpecialities()
	{
      

            $specialties = CatSupplierSpecialty::orderBy('description')->where("show",1)->where('deleted_at', NULL)->get();						
        
            return response()->json($specialties, 200);


	}
    protected function updateStatusEFO(Request $request){
        if(Auth::check()){
            
            $prov=  DmiabaSupplierRegistration::find($request["id"]);

            $prov->efo= $request["efo"];
            $prov->save();
            $time= time();
            if(isset($request["delete"])){
                $delFile= $request["delete"];
                if(Storage::disk("EFO")->exists($delFile)) {

                    unlink("../Storage/app/Proveedores/EFO/$delFile");
                    DmiabaDocumentsSupplier::where('name', $delFile)
                    ->where('dmiaba_supplier_registration_id', $request["id"])
                    ->update(['deleted_at' => Carbon::now()]);
                }
            }
            $data["subject"] = "Estatus: Nuevo proveedor";

            $adm = DB::table("cat_supplier_responsables")->where("position","Administrador")
            ->select('mail')->first();


            if($request->file('file')){
                $uploaded_file= $request->file('file');
                $original_ext = $uploaded_file->getClientOriginalExtension();
                $file_name=$request["id"]."_".$time."_file_EFO.".$original_ext;
                $doc= new DmiabaDocumentsSupplier();
                if( Storage::disk("EFO")->putFileAs("/", $uploaded_file, $file_name)){
                    $doc->name=$file_name;
                    $url= Storage::disk("EFO")->url($file_name);
                    $doc->url= $url;
                    $doc->cat_document_supplier_id= 11;
                    $doc->dmiaba_supplier_registration_id= $request["id"];
                    $doc->save();
                  }

                  if($request["efo"]=="DEFINITIVO"){
                    // $subject = "Estatus: nuevo proveedor";
                    $data["content"] = "no paso el filtro de contaloría.";
                    $data["data"] = DmiabaSupplierRegistration::find($request["id"]);

                    $usrmail = PersonalIntelisis::where("usuario_ad", $data["data"]["user"]);
                    // $mails = DB::table("cat_supplier_responsables")->where("position","Administrador")
                    // ->select('mail')->first();
                    $mails = array($usrmail, $adm->mail);

                    $this->sendEmail->EFO($data, $mails);
                }else if($request["efo"]=="PREVENTIVO"){
                    // $subject = "Estatus: nuevo proveedor";
                    $data["content"] = "ha pasado el filtro de contraloría con el estatus";

                    $data["data"] = DmiabaSupplierRegistration::find($request["id"]);

                    // $mails = DB::table("cat_supplier_responsables")->where("position","Administrador")
                    // ->select('mail')->first();
                    

                    $this->sendEmail->EFO($data, $adm->mail);
                }
            }
            if($request["efo"]=="O.K."){
                // $data["subject"] = "Estatus: Nuevo proveedor";
                $data["content"] = "ha pasado el filtro de contraloría con el estatus";
                $data["data"] = DmiabaSupplierRegistration::find($request["id"]);
                // $mails = DB::table("cat_supplier_responsables")->where("position","Administrador")
                // ->select('mail')->first();

                // // $mails= [
                // //     'irving.altuzar@grupodmi.com.mx'
                // // ];
                $this->sendEmail->EFO($data, $adm->mail);

            } 
        return response()->json(['success'=>'Se ha actualizado EFO.'],200);
    }else{
        return response()->json(null, 500); 

    }
    }
}
