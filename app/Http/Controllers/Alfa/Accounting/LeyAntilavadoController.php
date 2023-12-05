<?php

namespace App\Http\Controllers\Alfa\Accounting;

use Illuminate\Http\Request;
use App\Models\AccountingCompany;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\DmiAccgAntiMoneyLaunderingLaw;
class LeyAntilavadoController extends Controller
{
    protected function getDataAntilavado(Request $request){
        if(Auth::check()){
            $month='';
            $year='';
            switch ($request["month"]) {
                    case 'ENERO':
                        $month='DICIEMBRE';
                        break;
                    case 'FEBRERO':
                        $month='ENERO';
                        break;
                    case 'MARZO':
                        $month='FEBRERO';
                        break;
                    case 'ABRIL':
                        $month='MARZO';
                        break;
                    case 'MAYO':
                        $month='ABRIL';
                        break;
                    case 'JUNIO':
                        $month='MAYO';
                        break;
                    case 'JULIO':
                        $month='JUNIO';
                        break;
                    case 'AGOSTO':
                        $month='JULIO';
                        break;
                    case 'SEPTIEMBRE':
                        $month='AGOSTO';
                        break;
                    case 'OCTUBRE':
                        $month='SEPTIEMBRE';
                        break;
                    case 'NOVIEMBRE':
                        $month='OCTUBRE';
                        break;
                    case 'DICIEMBRE':
                        $month='NOVIEMBRE';
                        break;
            }
            $res= DmiAccgAntiMoneyLaunderingLaw::with('getCompany')->where('date_send', 'like', '%' . $request["year"] . '%')
            ->where('month',$month)->get();

            $array["data"]=$res;
            $array["month"]=$month;
            if($month=='DICIEMBRE'){
                $request["year"]= $request["year"]- 1 ;

            }
            $array["year"]= $request["year"];

            return response()->json($array,200);

         }else{

            return response()->json(['error'=>'No tienes Sesion'],200);
            }
    }
    protected function getCompaniesLaw(){
        if(Auth::check()){
            $res= AccountingCompany::where("has_law",1)->get();

            return response()->json($res,200);

         }else{

            return response()->json(['error'=>'No tienes Sesion'],200);
            }

    }
    protected function addAntilavado(Request $request){
        if(Auth::check()){

            $law= new DmiAccgAntiMoneyLaunderingLaw();
            $law->dmiaccg_company_id= $request["company_id"];
            $law->month= $request["month"];
            $law->type= $request["type"];
            $law->no_folio= $request["no_folio"];
            $law->date_send= $request["date_send"];
            $law->status_send= $request["status_send"];
            $law->amount= $request["amount"];
            $law->person_object_send= $request["person_object_send"];
            $law->full_expedient= $request["full_expedient"];
            $law->vulnerable_activity= $request["vulnerable_activity"];
            $law->save();

            return response()->json(['success'=>'Se ha creado registro antilavado.'],200);


         }else{

            return response()->json(['error'=>'No tienes Sesion'],200);
            }

    }

	public function saveBulkLoad(Request $request){
		if(Auth::check()){

			try {
				foreach ($request->data['excel_data'] as $key => $item) {
					$law= new DmiAccgAntiMoneyLaunderingLaw();
					$law->dmiaccg_company_id= $request->data['company_id'];
					$law->month= $item["month"];
					$law->type= $item["notice_type"];
					$law->no_folio= $item["folio"];
					$law->date_send= Carbon::parse($item["send_date"])->format("Y-m-d");
					$law->status_send= $item["status"];
					$law->amount= $item["amount"];
					$law->person_object_send= $item["object_delivery"];
					$law->full_expedient= $item["complete_record"];
					$law->vulnerable_activity= $item["vulnerable_activity"];
					$law->save();
				}

				return ["success"=> 1, "message"=>'La carga se a realizado con éxito'];
			} catch (\Throwable $th) {
				return ["success"=> 2, "message"=>'Error al cargar los datos'];
			}






        }else{
			return ["success"=> 0, "message"=>'No tienes Sesión activa'];
		}
	}

    protected function updateAntilavado(Request $request){
        if(Auth::check()){

            $law= DmiAccgAntiMoneyLaunderingLaw::find($request["id"]);
            $law->dmiaccg_company_id= $request["company_id"];
            $law->month= $request["month"];
            $law->type= $request["type"];
            $law->no_folio= $request["no_folio"];
            $law->date_send= $request["date_send"];
            $law->status_send= $request["status_send"];
            $law->amount= $request["amount"];
            $law->person_object_send= $request["person_object_send"];
            $law->full_expedient= $request["full_expedient"];
            $law->vulnerable_activity= $request["vulnerable_activity"];
            $law->save();

            return response()->json(['success'=>'Se ha modificado registro antilavado.'],200);


         }else{

            return response()->json(['error'=>'No tienes Sesion'],200);
            }

    }
}
