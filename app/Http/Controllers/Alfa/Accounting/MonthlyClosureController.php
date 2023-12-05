<?php

namespace App\Http\Controllers\Alfa\Accounting;

use App\Repositories\PersonalRepository;
use App\Http\Controllers\Controller;
use App\Models\AccountingCompany;
use App\Models\Accounting\MonthlyClosure;
use Illuminate\Http\Request;
use App\Http\Requests\MonthlyClosureRequest;
use App\Repositories\GeneralFunctionsRepository;
use App\Models\DmiControlProcedureValidation;
use App\Models\PersonalIntelisis;
use Carbon\Carbon;

class MonthlyClosureController extends Controller
{

	public function __construct(){
        $this->middleware('guest',['only'=>'ShowLogin']);
		$this->GeneralFunctionsRepository = new GeneralFunctionsRepository();
    }

    public function listMonthlyClosure(Request $request){

        $order_by = isset($request->order_by) ? $request->order_by : 'desc';
        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';
        $search = isset($request->search) ? $request->search : null;
		$month = isset($request->month) ? $request->month : Carbon::parse(Carbon::now())->format("m");
		$year = isset($request->year) ? $request->year : Carbon::parse(Carbon::now())->format("Y");

		//Se verifica si es gerente
		$is_manager = PersonalIntelisis::where('usuario_ad',auth()->user()->usuario)
							->where('position_company','like','%GERENTE%')
							->where('status', 'like', "%Alta%")->first();

		// Se verifica si el gerente puede visualizarlo todo
		$view_all = DmiControlProcedureValidation::where('key',"MonthlyClosure_view all-usuario_ad")
													->where('value',auth()->user()->usuario)->first();
		//dd($is_manager);

		if(isset($search) && strlen($search) > 0){
			$list = MonthlyClosure::with(['accounting_companies','accounting_companies.manager','accounting_companies.accountant','accounting_companies.workStation'])
					->where(
						function ($q) use ($search){
							return $q->whereRelation('accounting_companies.manager',function($q) use ($search){
								return $q->orWhere('name', 'like', "%$search%")
										->orWhere('last_name', 'like', "%$search%");
							})
							->orWhereRelation('accounting_companies.accountant',function($q) use ($search){
								return $q->orWhere('name', 'like', "%$search%")
										->orWhere('last_name', 'like', "%$search%");
							})
							->orWhereRelation('accounting_companies.workStation',function($q) use ($search){
								return $q->orWhere('description', 'like', "%$search%");
							})
							->orWhereRelation('accounting_companies',function($q) use ($search){
								return $q->orWhere('business_name', 'like', "%$search%");
							});
						}
					)
					->where('month',$month)
					->where('year',$year);
					if($is_manager != null && $view_all == null){
						$list = $list->where(
							function ($q) use ($is_manager){
								return $q->whereRelation('accounting_companies',function($q) use ($is_manager){
									return $q->orWhere('manager_id',$is_manager->usuario_ad);
								});
							}
						);
					}

					$list = $list->orderBy('created_at',$order_by)
					->Paginate($limit);
        }else{
			if($is_manager != null && $view_all == null){
				$list = MonthlyClosure::with(['accounting_companies'])
				->where(
					function ($q) use ($is_manager){
						return $q->whereRelation('accounting_companies',function($q) use ($is_manager){
							return $q->orWhere('manager_id',$is_manager->usuario_ad);
						});
					}
				)
				->where('month',$month)
				->where('year',$year)
				->orderBy('created_at',$order_by)
				->Paginate($limit);

			}else{

				$list = MonthlyClosure::with(['accounting_companies',])
				->where('month',$month)
				->where('year',$year)
				->orderBy('created_at',$order_by)
				->Paginate($limit);
			}

        }

        $list->setPath('/accounting/monthly-closure/list');

        return $list;


    }



	public function addRecord(MonthlyClosureRequest $request){
		$file_name_accounting = null;
		$file_name_fiscal = null;
		$file_name_payment = null;

		$exist = MonthlyClosure::where('dmi_accounting_companies_id',$request->dmi_accounting_companies_id)
								->where('month',$request->month)
								->where('year',$request->year)
								->first();

		//return $request->all();
		if($exist == null){
			// START - Se guardan los archivos cargados
			$folder_path_storage = 'storage/documents/accounting/monthly_closure';
			$folder_path = '/accounting/monthly_closure/';
			if(!file_exists($folder_path_storage)) {
				mkdir($folder_path_storage, 0777,true);
			}


			if($request->file('file_accounting') !== 'null' && $request->file('file_accounting') != ''){
				// Se almacena la imagen
				$file = $request->file('file_accounting');
				$file_name_accounting = time()."_".$file->getClientOriginalName();
				$path = $folder_path.$file_name_accounting;
				\Storage::disk('documents')->put($path,\File::get($file));

			}

			if($request->file('file_fiscal') !== 'null' && $request->file('file_fiscal') != ''){
				// Se almacena la imagen
				$file = $request->file('file_fiscal');
				$file_name_fiscal = time()."_".$file->getClientOriginalName();
				$path = $folder_path.$file_name_fiscal;
				\Storage::disk('documents')->put($path,\File::get($file));

			}

			if($request->file('file_payment') !== 'null' && $request->file('file_payment') != ''){
				// Se almacena la imagen
				$file = $request->file('file_payment');
				$file_name_payment = time()."_".$file->getClientOriginalName();
				$path = $folder_path.$file_name_payment;
				\Storage::disk('documents')->put($path,\File::get($file));

			}
			// END - Se guardan los archivos cargados

			$newRecord = new MonthlyClosure();
			$newRecord->dmi_accounting_companies_id = $request->dmi_accounting_companies_id;
			$newRecord->month = $request->month;
			$newRecord->year = $request->year;
			$newRecord->date_accounting = $request->date_accounting != 'null' ? $request->date_accounting : null;
			$newRecord->date_fiscal = $request->date_fiscal != 'null' ? $request->date_fiscal : null;
			$newRecord->date_payment = $request->date_payment != 'null' ? $request->date_payment : null;
			$newRecord->file_accounting = $file_name_accounting != null ? ($folder_path_storage.'/'.$file_name_accounting) : null;
			$newRecord->file_fiscal = $file_name_fiscal != null ? ($folder_path_storage.'/'.$file_name_fiscal) : null;
			$newRecord->file_payment = $file_name_payment != null ? ($folder_path_storage.'/'.$file_name_payment) : null;
			$newRecord->observations = $request->observations != 'null' ? $request->observations : null;
			$newRecord->save();

			if($newRecord != null){
				return ['success' => 1, "data" => $newRecord];
			}else{
				return ['success' => 0, 'message' => "Error al crear el registro."];
			}
		}else{
			return ['success' => 0, 'message' => "Ya existe el cierre mensual de esta empresa con el mes $request->month y año $request->year"];
		}



	}

	public function updateRecord(MonthlyClosureRequest $request){
		$file_name_accounting = null;
		$file_name_fiscal = null;
		$file_name_payment = null;

		$record = MonthlyClosure::find($request->id);


		// START - Se guardan los archivos cargados
		$folder_path_storage = 'storage/documents/accounting/monthly_closure';
		$folder_path = '/accounting/monthly_closure/';
		if(!file_exists($folder_path_storage)) {
			mkdir($folder_path_storage, 0777,true);
		}

		if($request->file('file_accounting') !== 'null' && $request->file('file_accounting') != ''){
			// Se almacena la imagen
			$file = $request->file('file_accounting');
			$file_name_accounting = time()."_".$file->getClientOriginalName();
			$path = $folder_path.$file_name_accounting;
			\Storage::disk('documents')->put($path,\File::get($file));
		}

		if($request->file('file_fiscal') !== 'null' && $request->file('file_fiscal') != ''){
			// Se almacena la imagen
			$file = $request->file('file_fiscal');
			$file_name_fiscal = time()."_".$file->getClientOriginalName();
			$path = $folder_path.$file_name_fiscal;
			\Storage::disk('documents')->put($path,\File::get($file));
		}

		if($request->file('file_payment') !== 'null' && $request->file('file_payment') != ''){
			// Se almacena la imagen
			$file = $request->file('file_payment');
			$file_name_payment = time()."_".$file->getClientOriginalName();
			$path = $folder_path.$file_name_payment;
			\Storage::disk('documents')->put($path,\File::get($file));
		}
		// END - Se guardan los archivos cargados

		$record->month = $request->month;
		$record->year = $request->year;
		$record->date_accounting = $request->date_accounting != 'null' ? $request->date_accounting : null;
		$record->date_fiscal = $request->date_fiscal != 'null' ? $request->date_fiscal : null;
		$record->date_payment = $request->date_payment != 'null' ? $request->date_payment : null;

		if($record->file_accounting !== null && $file_name_accounting != null){
			$this->deleteFile($record->id,'accounting');
			$record->file_accounting = $folder_path_storage.'/'.$file_name_accounting;

		}else if($record->file_accounting == null && $file_name_accounting != null){
			$record->file_accounting = $folder_path_storage.'/'.$file_name_accounting;
		}

		if($record->file_fiscal !== null && $file_name_fiscal != null){
			$this->deleteFile($record->id,'fiscal');
			$record->file_fiscal = $folder_path_storage.'/'.$file_name_fiscal;

		}else if($record->file_fiscal == null && $file_name_fiscal != null){
			$record->file_fiscal = $folder_path_storage.'/'.$file_name_fiscal;
		}

		if($record->file_payment !== null && $file_name_payment != null){
			$this->deleteFile($record->id,'payment');
			$record->file_payment = $folder_path_storage.'/'.$file_name_payment;

		}else if($record->file_payment == null && $file_name_payment != null){
			$record->file_payment = $folder_path_storage.'/'.$file_name_payment;
		}

		$record->observations = $request->observations != 'null' ? $request->observations : null;
		$record->save();

		if($record != null){
			return ['success' => 1, "data" => $record];
		}else{
			return ['success' => 0, 'message' => "Error al crear el registro."];
		}




	}

	public function deleteFile($_monthly_closure_id,$_field){
		$record = MonthlyClosure::where('id',$_monthly_closure_id)->first();

		if($record != null){
			if($_field == "accounting"){
				$this->GeneralFunctionsRepository->deleteFile(['url'=>$record->file_accounting]);
				$record->file_accounting = null;
				$record->save();
			}else if($_field == "fiscal"){
				$this->GeneralFunctionsRepository->deleteFile(['url'=>$record->file_fiscal]);
				$record->file_fiscal = null;
				$record->save();
			}else if($_field == "payment"){
				$this->GeneralFunctionsRepository->deleteFile(['url'=>$record->file_payment]);
				$record->file_payment = null;
				$record->save();
			}

			return ['success' => 1, 'message' => "Archivo eliminado con éxito."];

		}else{
			return ["success" => 0, "message" => "No existe el elemento seleccionado."];
		}

	}

	public function singleMonthlyClosure($_id){

        $record = MonthlyClosure::with(['accounting_companies'])->find($_id);

		if($record != null){
			return ["success" => 1, "data" => $record];
		}else{
			return ["success" => 0, "message" => "No existe el registro."];
		}


    }

	public function getGraphic(Request $request)
	{
		$finish_date = Carbon::parse($request->finish_date['value']);

		$electronic_accounting = MonthlyClosure::with(['accounting_companies'])
			->whereRelation('accounting_companies', function ($query) use ($request) {
				return $query->where('accountant_id', 'like', "%$request->accountant_id%");
			})
			->where(function ($q) use ($request){
				$q->where('month', $request->month)->where('year', $request->year);
			})
			->get();

		$in_time = $electronic_accounting->filter( function ($q) use ($finish_date) {
			return $q->date_accounting <= $finish_date;
		})->count();

		$lately = $electronic_accounting->filter( function ($q) use ($finish_date) {
			return $q->date_accounting >= $finish_date;
		})->count();

		$data = [$in_time, $lately];

		return $data;
	}

	public function CuttingDay(){

		$day = DmiControlProcedureValidation::where('key',"MonthlyClosure-cutting_day-day")->first();

		if($day != null){
			return ["success" => 1, "data" => $day->value];
		}else{
			return ["success" => 0, "message" =>"No tiene día de corte"];
		}


	}

	/* ************************************		START - FECHAS DE CORTE	************************************ */

	public function listCutOffDate(){
		$list = DmiControlProcedureValidation::where("key","like","%MonthlyClosure_cutoff date_month%")->get();
		return $list;

		if($list != null){
			return ["success" => 1, "data" => $list];
		}else{
			return ["success" => 0, "message" =>"No tiene Fechas de corte de Cierre mensual"];
		}
	}

	public function editCutOffDate(Request $request){

		$edit = DmiControlProcedureValidation::where('id',$request->id)->first();

		if($edit != null){
			$edit->value = $request->day;
			$edit->save();

			return ["success" => 1, "data" => $edit];
		}else{
			return ["success" => 2, "message" => "No se encontro el registro solicitado."];
		}

	}

	public function graphicsCutOffDate(){
		$list = DmiControlProcedureValidation::where("key","like","%MonthlyClosure_cutoff date_month%")->get();

		$dates = [];
		forEach($list as $month){
			$date ="";
			$num_month = explode(" ",explode('-',$month['key'])[0])[2];

			if($num_month == 14){
				$date = date("Y")."-00-".$month['value'];
			}else{
				$date = date("Y")."-".$num_month."-".$month['value'];
			}

			$dates[] = ["id" => $num_month, 'value'=>$date];
		}

		return $dates;
	}

	/* ************************************		END - FECHAS DE CORTE	************************************ */

}
