<?php

namespace App\Http\Controllers\Alfa\RecursosHumanos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DmiRh\DmirhVacation;
use App\Models\DmiRh\DmirhWorkPermit;
use App\Models\DmiRh\DmirhPersonalJustification;
use App\Http\Controllers\GenericFunctionsController;
use App\Models\PersonalIntelisis;
use DateTime;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\DmiBucketSignature;
use App\Models\DmirhPersonalRequisition;
use Illuminate\Support\Facades\Storage;
use App\Models\CatRequisitionsAdmin;
use App\Models\SegLogin;
use App\Http\Controllers\Alfa\PersonalRequisitions\PersonalRequisitionController;


class AuthorisationController extends Controller
{
    private $GenericFunctionsController,$PersonalRequisitionController;

	public function __construct(GenericFunctionsController $_GenericFunctionsController, PersonalRequisitionController $_PersonalRequisitionController)
	{
        $this->middleware('guest',['only'=>'ShowLogin']);
        $this->GenericFunctionsController = $_GenericFunctionsController;
        $this->GenericFunctionsController = $_GenericFunctionsController;
        $this->PersonalRequisitionController = $_PersonalRequisitionController;
	}

    public function list(Request $request){
        

        $seg_seccion_vacationes = 11;
        $seg_seccion_permisos = 9;
        $list = collect();
        $list_vacations = collect();
        $list_workpermit = collect();
        $list_justifications = collect();
        $list_req_pendientes = collect();
        $list_req_validacion = collect();

        $order_by = isset($request->order_by) ? $request->order_by : 'desc';
        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';
        $search = isset($request->search) ? $request->search : null;
        $subject = isset($request->subject) ? $request->subject : "all";

        $profile = PersonalIntelisis::where("usuario_ad",Auth::user()->usuario)
									->where('status','ALTA')
									->first();

        
        //Vacaciones
        if($subject == "vacaciones" || $subject == "all"){
            
            // Se verifica si puede firmar en lugar de alguien más
		    $check_signature_behalf = $this->GenericFunctionsController->checkUserSignOnBehalf($seg_seccion_vacationes,$profile->usuario_ad);

            $list_vacations = DmirhVacation::with(['personal_intelisis.dmirh_personal_time'])
                                    ->join('dmi_bucket_signatures as bs','bs.origin_record_id','=','dmirh_vacation.id')
                                    ->where('bs.seg_seccion_id',$seg_seccion_vacationes)
                                    ->where('dmirh_vacation.status','solicitado')
                                    ->where('bs.status','pendiente');

									if($check_signature_behalf['is_valid'] == 1){
										$list_vacations = $list_vacations->whereIn('bs.personal_intelisis_usuario_ad',array_merge($check_signature_behalf['data'],[$profile->usuario_ad]));
									}else{
										$list_vacations = $list_vacations->where('bs.personal_intelisis_usuario_ad',$profile->usuario_ad);
									}

                                    $list_vacations = $list_vacations->whereNull('bs.signed_date')
                                    ->where('bs.order', '>=' , 2)
									->where(
										function ($q) use ($search){
											return $q->whereRelation('personal_intelisis',function($q) use ($search){
												return $q->orWhere('name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%");
											})
                                            ->orWhereRaw("(
                                                dmirh_vacation.date_request like '%$search%'
                                            )");
                                            
										}
									)
                                    ->orderBy('dmirh_vacation.created_at',$order_by)
                                    ->select('dmirh_vacation.*',DB::raw("'vacaciones' as type_record"))
                                    ->get();
        }

        if($subject == "permisos" || $subject == "all"){
            // Se verifica si puede firmar en lugar de alguien más
            $check_signature_behalf = $this->GenericFunctionsController->checkUserSignOnBehalf($seg_seccion_vacationes,$profile->usuario_ad);

            $list_workpermit = DmirhWorkPermit::with(['personal_intelisis.dmirh_personal_time','type_permit','permit_concept'])
                                    ->join('dmi_bucket_signatures as bs','bs.origin_record_id','=','dmirh_work_permits.id')
                                    ->where('bs.seg_seccion_id',$seg_seccion_permisos)
                                    ->where('dmirh_work_permits.status','solicitado')
                                    ->where('bs.status','pendiente');

									if($check_signature_behalf['is_valid'] == 1){
										$list_workpermit = $list_workpermit->whereIn('bs.personal_intelisis_usuario_ad',array_merge($check_signature_behalf['data'],[$profile->usuario_ad]));
									}else{
										$list_workpermit = $list_workpermit->where('bs.personal_intelisis_usuario_ad',$profile->usuario_ad);
									}

                                    $list_workpermit = $list_workpermit->whereNull('bs.signed_date')
                                    ->where('bs.order', '>=' , 2)
									->where(
										function ($q) use ($search){
											return $q->whereRelation('personal_intelisis',function($q) use ($search){
												return $q->orWhere('name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%");
											})
                                            ->orWhereRaw("(
                                                dmirh_work_permits.date_request like '%$search%'
                                            )");
										}
									)
                                    ->orderBy('dmirh_work_permits.created_at',$order_by)
                                    ->select('dmirh_work_permits.*',DB::raw("'permiso' as type_record"))
                                    ->get();
        
        
        }
        
        if($subject == "justificaciones" || $subject == "all"){

            $usuarios = PersonalIntelisis::where('top_plaza_id',$profile->plaza_id)
                                            ->where('status', "ALTA")
                                            ->get()
                                            ->pluck('usuario_ad')
                                            ->toArray();

            $list_justifications = DmirhPersonalJustification::with(['personal_intelisis','dmirh_cat_type_justification'])
                                                        ->whereIn("user",$usuarios)
                                                        ->where("approved_by",null)
                                                        ->where(
                                                            function ($q) use ($search){
                                                                return $q->whereRelation('personal_intelisis',function($q) use ($search){
                                                                    return $q->orWhere('name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%");
                                                                })
                                                                ->orWhereRaw("(
                                                                    dmirh_personal_justification.date like '%$search%'
                                                                )");
                                                            }
                                                        )
                                                        ->orderBy("date","desc")
                                                        ->select('dmirh_personal_justification.*','dmirh_personal_justification.created_at as date_request',DB::raw("'justificacion' as type_record"))
                                                        ->get();

        }
        
        if($subject == "requisiciones" || $subject == "all"){
            $list_req_pendientes = $this->getRequisitionsMyPersonalPendientes($search);
            $list_req_validacion = $this->getPersonalRequisitionValidation($search);
        }


        $list = $list->concat($list_vacations);
        $list = $list->concat($list_workpermit);
        $list = $list->concat($list_justifications);
        $list = $list->concat($list_req_pendientes);
        $list = $list->concat($list_req_validacion);

        if($order_by == 'desc'){
            $list = $list->sortByDesc('created_at')->values();
        }else{
            $list = $list->sortBy('created_at')->values();
        }
        
        

        $collection = new Collection($list);

        $items = $collection->forPage($request->page,$limit);
        $totalResult = $collection->count();
        $currentPage = $request->page ?: (Paginator::resolveCurrentPage() ?: 1);

        return new LengthAwarePaginator(
            $items,
            $totalResult,
            $limit,
            $currentPage,
            [
                'path' => url()->current(),
                'pageName' => 'page',
            ]

        );
    }

    protected function getRequisitionsMyPersonalPendientes($_search){

        if(Auth::check()){
            
            $array=[];
            $user = auth()->user()->usuario;
            

            $signs= DmiBucketSignature::where('seg_seccion_id',12)
                ->where('personal_intelisis_usuario_ad',$user)
                ->where(function($query) {
                    $query->where("status","pendiente");
                })->where("seg_seccion_id",12)->orderBy("origin_record_id","desc")->get();

            $res = DB::table("dmicontrol_signatures_behalves")->where("behalf_usuario_ad",$user)->where('seg_seccion_id',12)
                ->select('usuario_ad');
            
            $signs2= DmiBucketSignature::where('seg_seccion_id',12)
                ->whereIn('personal_intelisis_usuario_ad', $res)
                ->where(function($query) {
                    $query->where("status","pendiente");
                })->where("seg_seccion_id",12)->orderBy("origin_record_id","desc")->get();

            if(count($signs)>0){
                foreach ($signs as $key => $value) {
                    $res2=DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment','personal_intelisis'])
                                                    ->where("id",intval($value->origin_record_id))
                                                    ->where("status","recaudar firmas")
                                                    ->where(
                                                        function ($q) use ($_search){
                                                            return $q->whereRelation('personal_intelisis',function($q) use ($_search){
                                                                return $q->orWhere('name', 'like', "%$_search%")->orWhere('last_name', 'like', "%$_search%");
                                                            })
                                                            ->orWhereRaw("(dmirh_personal_requisition.vacancy like '%$_search%'
                                                                        or dmirh_personal_requisition.date like '%$_search%'
                                                            )");
                                                        }
                                                    )
                                                    ->select('dmirh_personal_requisition.*',DB::raw("'requisicion' as type_record"))
                                                    ->first();

                    if(isset($res2)){
                        array_push($array,$res2);
                        end($array);
                        $k = key($array);
                        if($res2!= null){
                            $array[$k]['sign_status'] = $value->status;
                        }
                        if($res2->file!= null){
                            $file= Storage::disk("Requisitions")->url($res2->file);
                            $array[$k]['url']=$file;
                        }else{
                            $array[$k]['url']=null;
                        }
                    }
                }
            }

            if(count($signs2)>0){
                $type= DB::table('dmicontrol_signatures_behalves')
                                    ->join('dmicontrol_process', 'dmicontrol_signatures_behalves.dmi_control_process_id', '=', 'dmicontrol_process.id')
                                    ->where('dmicontrol_signatures_behalves.behalf_usuario_ad',$user)
                                    ->where('seg_seccion_id',12)
                                    ->select('dmicontrol_process.name')->distinct()
                                    ->get();

                foreach ($signs2 as $key => $value) {
                    if(count($type)>1){
                        $res3=DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment','personal_intelisis'])
                                                        ->where("id",intval($value->origin_record_id))
                                                        ->where(function($query) use($type) {
                                                            $query->where("type",$type[0]->name)
                                                            ->orWhere("type",$type[1]->name);
                                                        })->where("status","recaudar firmas")
                                                        ->where(
                                                            function ($q) use ($_search){
                                                                return $q->whereRelation('personal_intelisis',function($q) use ($_search){
                                                                    return $q->orWhere('name', 'like', "%$_search%")->orWhere('last_name', 'like', "%$_search%");
                                                                })
                                                                ->orWhereRaw("(dmirh_personal_requisition.vacancy like '%$_search%'
                                                                                or dmirh_personal_requisition.date like '%$_search%'
                                                                )");
                                                            }
                                                        )
                                                        ->select('dmirh_personal_requisition.*',DB::raw("'requisicion' as type_record"))
                                                        ->first();
                    }else{
                        $res3=DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment','personal_intelisis'])
                        ->where("id",intval($value->origin_record_id))->where("type",$type[0]->name)
                        ->where("status","recaudar firmas")
                        ->where(
                            function ($q) use ($_search){
                                return $q->whereRelation('personal_intelisis',function($q) use ($_search){
                                    return $q->orWhere('name', 'like', "%$_search%")->orWhere('last_name', 'like', "%$_search%");
                                })
                                ->orWhereRaw("(dmirh_personal_requisition.vacancy like '%$_search%'
                                            or dmirh_personal_requisition.date like '%$_search%'
                                )");
                            }
                        )
                        ->select('dmirh_personal_requisition.*',DB::raw("'requisicion' as type_record"))
                        ->first();

                    }

                    if($res3!=null){
                        array_push($array,$res3);
                        end($array);
                        $k = key($array);
                        if($res3!= null){
                            $array[$k]['sign_status'] = $value->status;
                        }
                        if($res3->file!= null){
                            $file= Storage::disk("Requisitions")->url($res3->file);
                            $array[$k]['url']=$file;
                        }else{
                            $array[$k]['url']=null;
                        }
                    }
                }
            }
            
            return $array;

        }else{
            return null;

        }
    }

    protected function getPersonalRequisitionValidation($_search){
        $res = collect();

        if(Auth::check()){
            $user = auth()->user()->usuario;
            $has_access= SegLogin::where('subsecId',53)->where('loginUsr',$user)->first();

            if($has_access != null){
                

                //is Admin
                $adm= CatRequisitionsAdmin::where("responsable_user",$user)->first();
                
                if(isset($adm) || $adm!= ""){
                    $res=DmirhPersonalRequisition::with(['dmi_control_email_domain','personal_intelisis'])
                                                    ->where("status","Validacion")
                                                    ->where(
                                                        function ($q) use ($_search){
                                                            return $q->whereRelation('personal_intelisis',function($q) use ($_search){
                                                                return $q->orWhere('name', 'like', "%$_search%")->orWhere('last_name', 'like', "%$_search%");
                                                            })
                                                            ->orWhereRaw("(dmirh_personal_requisition.vacancy like '%$_search%'
                                                                        or dmirh_personal_requisition.date like '%$_search%'
                                                            )");
                                                        }
                                                    )
                                                    ->select('dmirh_personal_requisition.*',DB::raw("'requisicion' as type_record"))
                                                    ->orderBy("date","desc")->orderBy("id","desc")
                                                    ->get();
                }else{

                    $loc= PersonalIntelisis::where("usuario_ad",$user)->where("status","ALTA")->first();
                    
                    if($loc->location=="CORPORATIVO"){
                        $res= DmirhPersonalRequisition::with(['dmi_control_email_domain','personal_intelisis'])->join('personal_intelisis', 'personal_intelisis.usuario_ad', '=', 'dmirh_personal_requisition.user')
                        ->select('dmirh_personal_requisition.*')
                        ->where('personal_intelisis.status','ALTA')
                        ->where("dmirh_personal_requisition.status","validacion")
                        ->where('dmirh_personal_requisition.vacancy', 'like', "%$_search%")
                        ->where(function($query) use ($_search) {
                            $query->where("personal_intelisis.location","CORPORATIVO")
                            ->orWhere("personal_intelisis.location","FUNDACION")
                            ->orWhere("personal_intelisis.location","LOS ROBLES")
                            ->orWhere("personal_intelisis.location","MAYAKOBA")
                            ->orWhere('personal_intelisis.name', 'like', "%$_search%")
                            ->orWhere('personal_intelisis.last_name', 'like', "%$_search%")
                            ->orWhere('dmirh_personal_requisition.date', 'like', "%$_search%");
                        })
                        ->select('dmirh_personal_requisition.*',DB::raw("'requisicion' as type_record"))
                        ->orderBy("dmirh_personal_requisition.date","desc")
                        ->get();
                    }else{
                        //Se busca a que ubicaciones tiene acceso la coordinación
                        $locations = $this->PersonalRequisitionController->getCoordinadoraRRHHLocation();
                        
                        if($locations == null){
                            $locations = [$loc->location];
                        }

                        $res= DmirhPersonalRequisition::with(['dmi_control_email_domain','personal_intelisis'])->join('personal_intelisis', 'personal_intelisis.usuario_ad', '=', 'dmirh_personal_requisition.user')
                        ->where('personal_intelisis.status','ALTA')
                        ->where("dmirh_personal_requisition.status","validacion")
                        ->whereIn('personal_intelisis.location',$locations)
                        ->where('dmirh_personal_requisition.vacancy', 'like', "%$_search%")
                        ->where('personal_intelisis.name', 'like', "%$_search%")
                        ->orWhere('personal_intelisis.last_name', 'like', "%$_search%")
                        ->orWhere('dmirh_personal_requisition.date', 'like', "%$_search%")
                        ->select('dmirh_personal_requisition.*',DB::raw("'requisicion' as type_record"))
                        ->orderBy("dmirh_personal_requisition.date","desc")->get();

                    }

                }

                return $res;

            }else{
                return $res;
            }
            
        }else{
            return $res;

        }
    }


}