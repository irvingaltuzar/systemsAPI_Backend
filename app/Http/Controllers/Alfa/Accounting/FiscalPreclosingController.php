<?php

namespace App\Http\Controllers\Alfa\Accounting;

use App\Repositories\PersonalRepository;
use App\Http\Controllers\Controller;
use App\Models\AccountingCompany;
use App\Models\Accounting\FiscalPreclosing;
use Illuminate\Http\Request;
use App\Http\Requests\FiscalPreclosingRequest;
use App\Repositories\GeneralFunctionsRepository;
use Carbon\Carbon;

class FiscalPreclosingController extends Controller
{

	public function __construct(){
        $this->middleware('guest',['only'=>'ShowLogin']);
		$this->GeneralFunctionsRepository = new GeneralFunctionsRepository();
    }

    public function listFiscalPreclosing(Request $request){

        $order_by = isset($request->order_by) ? $request->order_by : 'desc';
        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';
        $search = isset($request->search) ? $request->search : null;
		$month = isset($request->month) ? $request->month : Carbon::parse(Carbon::now())->format("m");
		$year = isset($request->year) ? $request->year : Carbon::parse(Carbon::now())->format("Y");

        if(isset($search) && strlen($search) > 0){
            $list = FiscalPreclosing::with(['accounting_companies','accounting_companies.manager','accounting_companies.accountant','accounting_companies.workStation'])
									->whereRelation('accounting_companies.manager',function($q) use ($search){
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
									})
									->where('month',$month)
									->where('year',$year)
									->orderBy('created_at',$order_by)
									->Paginate($limit);
        }else{
            $list = FiscalPreclosing::with(['accounting_companies'])
			->where('month',$month)
			->where('year',$year)
            ->orderBy('created_at',$order_by)
            ->Paginate($limit);
        }

        $list->setPath('/accounting/fiscal-preclosing/list');

        return $list;


    }



	public function addRecord(FiscalPreclosingRequest $request){
		$file_name_accounting = null;
		$file_name_fiscal = null;
		$file_name_payment = null;

		$exist = FiscalPreclosing::where('dmi_accounting_companies_id',$request->dmi_accounting_companies_id)
								->where('month',$request->month)
								->where('year',$request->year)
								->first();

		if($exist == null){

			$newRecord = new FiscalPreclosing();
			$newRecord->dmi_accounting_companies_id = $request->dmi_accounting_companies_id;
			$newRecord->month = $request->month;
			$newRecord->year = $request->year;
			$newRecord->accounting_utility = $request->accounting_utility != 'null' ? $request->accounting_utility : null;
			$newRecord->tax_utility = $request->tax_utility != 'null' ? $request->tax_utility : null;
			$newRecord->comments = $request->comments != 'null' ? $request->comments : null;
			$newRecord->save();

			if($newRecord != null){
				return ['success' => 1, "data" => $newRecord];
			}else{
				return ['success' => 0, 'message' => "Error al crear el registro."];
			}
		}else{
			return ['success' => 0, 'message' => "Ya existe el Precierre Fiscal de esta empresa con el mes $request->month y año $request->year"];
		}



	}

	public function updateRecord(FiscalPreclosingRequest $request){

		$record = FiscalPreclosing::find($request->id);

		// START - Se guardan los archivos cargados
		$folder_path_storage = 'storage/documents/accounting/fiscal-preclosing';
		$folder_path = '/accounting/fiscal-preclosing/';

		$record->month = $request->month;
		$record->year = $request->year;
		$record->accounting_utility = $request->accounting_utility != 'null' ? $request->accounting_utility : null;
		$record->tax_utility = $request->tax_utility != 'null' ? $request->tax_utility : null;
		$record->comments = $request->comments != 'null' ? $request->comments : null;
		$record->save();

		if($record != null){
			return ['success' => 1, "data" => $record];
		}else{
			return ['success' => 0, 'message' => "Error al crear el registro."];
		}




	}

	public function singleFiscalPreclosing($_id){

        $record = FiscalPreclosing::with(['accounting_companies'])->find($_id);

		if($record != null){
			return ["success" => 1, "data" => $record];
		}else{
			return ["success" => 0, "message" => "No existe el registro."];
		}


    }

}
