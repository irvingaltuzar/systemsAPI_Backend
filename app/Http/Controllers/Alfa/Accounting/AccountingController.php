<?php

namespace App\Http\Controllers\Alfa\Accounting;

use App\Repositories\PersonalRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccountingCompanyRequest;
use App\Http\Requests\StoreEAccountingRequest;
use App\Http\Requests\StoreInterimPaymentRequest;
use App\Http\Requests\StoreOverviewRequest;
use App\Models\AccountingCompany;
use App\Models\Accounting\MonthlyClosure;
use App\Models\CatEAccountingStatus;
use App\Models\CatOverview;
use App\Models\DmiaccgDiot;
use App\Models\DmiaccgDyp;
use App\Models\DmiAccgInterimPayments;
use App\Models\DmiEAccounting;
use App\Models\DmiOverviewActivities;
use App\Repositories\ToolsRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AccountingController extends Controller
{

	private $personalRepository, $toolRepository;

	public function __construct(PersonalRepository $personalRepository, ToolsRepository $toolRepository)
	{
		$this->personalRepository = $personalRepository;
		$this->toolRepository = $toolRepository;
	}

	public function fetchAllCompanies()
	{
		$accounting_companies = AccountingCompany::orderBy('created_at', 'asc')->get();

		return response()->json($accounting_companies);
	}

    public function fetchCompanies(Request $request)
	{
        $order_by = isset($request->order_by) ? $request->order_by : 'asc';

        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';

        $search = isset($request->search) ? $request->search : '';


        if(isset($search) && strlen($search) > 0){

            $accounting_companies = AccountingCompany::orWhereRelation('manager', function ($query) use ($search) {
								return $query->orWhere('name', 'like', "%$search%")
											->orWhere('last_name', 'like', "%$search%");
							})
							->orWhereRelation('accountant', function ($query) use ($search) {
								return $query->orWhere('name', 'like', "%$search%")
											->orWhere('last_name', 'like', "%$search%");
							})
							->orWhereHas('electronicAccounting', function ($query) use ($search) {
								return $query->where('date', 'like', "%$search%");
							})
							->orWhereHas('workStation', function ($query) use ($search) {
								return $query->where('description', 'like', "%$search%");
							})
							->orWhere('business_name', 'like', "%$search%")
							->orderBy('created_at',$order_by)
							->Paginate($limit);

        }else{

            $accounting_companies = AccountingCompany::with(['accountant'])->orderBy('created_at',$order_by)
										->Paginate($limit);

        }

        $accounting_companies->setPath('/accounting/fetch-companies');

        return $accounting_companies;
	}

    public function fetchElectronicAccounting(Request $request)
	{

		$year = isset($request->year) ? $request->year : Carbon::parse(Carbon::now())->format("Y");

		$month = isset($request->month) ? ($request->month) : Carbon::now();

        $order_by = isset($request->order_by) ? $request->order_by : 'asc';

        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';

        $search = isset($request->search) ? $request->search : '';

		if ($month == '14') {
			if(isset($search) && strlen($search) > 0){

				$electronic_accounting = DmiEAccounting::with(['company'])
											->where(
												function ($q) use ($search){
													return $q->orWhereRelation('company.manager', function ($query) use ($search) {
																	return $query->orWhere('name', 'like', "%$search%")
																				->orWhere('last_name', 'like', "%$search%");
																})
																->orWhereRelation('company.accountant', function ($query) use ($search) {
																	return $query->orWhere('name', 'like', "%$search%")
																				->orWhere('last_name', 'like', "%$search%");
																})
																->orWhereRelation('company', function ($query) use ($search) {
																	return $query->orWhere('business_name', 'like', "%$search%");
																})
																->orWhereHas('company.workStation', function ($query) use ($search) {
																	return $query->where('description', 'like', "%$search%");
																})
																->orWhereHas('status', function ($query) use ($search) {
																	return $query->where('description', 'like', "%$search%");
																})
																->orWhere('date', 'like', "%$search%")
																->orWhere('id_transaction_receipt', 'like', "%$search%");
												}
											)
											->where(
												function ($q) use ($year, $month){
													return $q->whereYear('date', ($year + 1));
												}
											)
											->where(
												function ($q) use ($year, $month){
													return $q->where('is_yearly', true);
												}
											)
											->Paginate($limit);
			}else{
				$electronic_accounting = DmiEAccounting::with(['company'])
											->whereYear('date', ($year + 1))
											->orderBy('date', $order_by)
											->where('is_yearly', true)
											->Paginate($limit);
			}
		} else {
			if(isset($search) && strlen($search) > 0){

				$electronic_accounting = DmiEAccounting::with(['company'])
											->where(
												function ($q) use ($search){
													return $q->orWhereRelation('company.manager', function ($query) use ($search) {
																	return $query->orWhere('name', 'like', "%$search%")
																				->orWhere('last_name', 'like', "%$search%");
																})
																->orWhereRelation('company.accountant', function ($query) use ($search) {
																	return $query->orWhere('name', 'like', "%$search%")
																				->orWhere('last_name', 'like', "%$search%");
																})
																->orWhereRelation('company', function ($query) use ($search) {
																	return $query->orWhere('business_name', 'like', "%$search%");
																})
																->orWhereHas('company.workStation', function ($query) use ($search) {
																	return $query->where('description', 'like', "%$search%");
																})
																->orWhereHas('status', function ($query) use ($search) {
																	return $query->where('description', 'like', "%$search%");
																})
																->orWhere('date', 'like', "%$search%")
																->orWhere('id_transaction_receipt', 'like', "%$search%");
												}
											)
											->where(
												function ($q) use ($year, $month){
													return $q->whereYear('date', $year)
																->whereMonth('date', $month + 1);
												}
											)
											->Paginate($limit);
			}else{
				$electronic_accounting = DmiEAccounting::with(['company'])
											->whereMonth('date', $month + 1)
											->whereYear('date', $year)
											->orderBy('date', $order_by)
											->Paginate($limit);

			}
		}

        $electronic_accounting->setPath('/accounting/fetch-e-accounting');

        return $electronic_accounting;
	}

    public function fetchOverviewsAccounting(Request $request)
	{

		$year = isset($request->year) ? $request->year : Carbon::parse(Carbon::now())->format("Y");

        $order_by = isset($request->order_by) ? $request->order_by : 'asc';

        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';

        $search = isset($request->search) ? $request->search : '';


        if(isset($search) && strlen($search) > 0){

            $overviews_accounting = DmiOverviewActivities::with(['company'])
										->orWhereRelation('company.manager', function ($query) use ($search) {
											return $query->orWhere('name', 'like', "%$search%")
														->orWhere('last_name', 'like', "%$search%");
										})
										->orWhereRelation('company.accountant', function ($query) use ($search) {
											return $query->orWhere('name', 'like', "%$search%")
														->orWhere('last_name', 'like', "%$search%");
										})
										->orWhereRelation('company', function ($query) use ($search) {
											return $query->orWhere('business_name', 'like', "%$search%");
										})
										->orWhereHas('overview', function ($query) use ($search) {
											return $query->where('description', 'like', "%$search%");
										})
										->orWhere('date', 'like', "%$search%")
										->orWhere('comments', 'like', "%$search%")
										->Paginate($limit);

        }else{

			$overviews_accounting = DmiOverviewActivities::with(['company'])
										->whereYear('date', $year)
										->orderBy('date', $order_by)
										->Paginate($limit);

        }

        $overviews_accounting->setPath('/accounting/fetch-overviews-accounting');

        return $overviews_accounting;
	}

    public function fetchInterimPayments(Request $request)
	{
		$year = isset($request->year) ? $request->year : Carbon::parse(Carbon::now())->format("Y");

		$month = isset($request->month) ? ($request->month + 1) : Carbon::now();

        $order_by = isset($request->order_by) ? $request->order_by : 'asc';

        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';

        $search = isset($request->search) ? $request->search : '';


        if(isset($search) && strlen($search) > 0){

            $interim = DmiAccgInterimPayments::with(['company'])
										->where(
											function ($q) use ($search){
												return $q->orWhereRelation('company.manager', function ($query) use ($search) {
																return $query->orWhere('name', 'like', "%$search%")
																			->orWhere('last_name', 'like', "%$search%");
															})
															->orWhereRelation('company.accountant', function ($query) use ($search) {
																return $query->orWhere('name', 'like', "%$search%")
																			->orWhere('last_name', 'like', "%$search%");
															})
															->orWhereRelation('company', function ($query) use ($search) {
																return $query->orWhere('business_name', 'like', "%$search%");
															})
															->orWhere('diot_date', 'like', "%$search%")
															->orWhere('dyp_date', 'like', "%$search%")
															->orWhere('diot_id_transaction_receipt', 'like', "%$search%")
															->orWhere('dyp_id_transaction_receipt', 'like', "%$search%");
											}
										)
										->where(
											function ($q) use ($year, $month){
												return $q->whereYear('diot_date', $year)
															->whereMonth('diot_date', $month);
											}
										)
										->Paginate($limit);

        }else{

			$interim = DmiAccgInterimPayments::with(['company'])
							->where(
								function ($q) use ($year, $month){
									return $q->whereYear('diot_date', $year)
												->whereMonth('diot_date', $month);
								}
							)
							->orderBy('diot_date', $order_by)
							->Paginate($limit);
        }

        $interim->setPath('/accounting/fetch-interim');

        return $interim;
	}


	public function fetchPersonal()
	{
		$personal = $this->personalRepository->getAccountantPersonal();

		return response()->json($personal);
	}

	public function getTools()
	{
		$erps = $this->toolRepository->getErp();

		$work_station = $this->toolRepository->getWorkStation();

		return response()->json([
			'erps' => $erps,
			'work_stations'	=> $work_station
		]);
	}

	public function storeCompany(StoreAccountingCompanyRequest $request)
	{
		$company = AccountingCompany::create([
						'cat_work_station_id' => $request['work_station_id'],
						'business_name' => $request['company_name'],
						'manager_id' => $request['manager_id'],
						'accountant_id' => $request['accountant_id'],
						'has_law' => $request['has_law'],
						'cat_erp_id' => $request['erp_id'],
					]);

		return response()->json($company);
	}

	public function storeEAccounting(StoreEAccountingRequest $request)
	{
		$company = DmiEAccounting::create([
						'accounting_company_id' => $request['company_id'],
						'cat_e_accounting_status_id' => $request['e_accounting_status'],
						'date' => $request['date'],
						'id_transaction_receipt' => $request['id_transaction'],
						'is_yearly' => $request['yearly'],
					]);

		return response()->json($company);
	}

	public function storeOverview(StoreOverviewRequest $request)
	{
		$company = DmiOverviewActivities::create([
						'accounting_company_id' => $request['company_id'],
						'cat_overview_id' => $request['overview_id'],
						'date' => $request['date'],
						'comments' => $request['comment']
					]);

		return response()->json($company);
	}

	public function storeInterimPayment(StoreInterimPaymentRequest $request)
	{
		$company = DmiAccgInterimPayments::create([
			'accounting_company_id' => $request['company_id'],
			'diot_date' => $request['diot_date'],
			'diot_id_transaction_receipt' => $request['diot_id_transaction'],
			'dyp_date' => $request['dyp_date'],
			'dyp_id_transaction_receipt' => $request['dyp_id_transaction'],
			'is_yearly' => $request['yearly'],
		]);

		return response()->json([$company]);
	}

	public function getEStatus()
	{
		$statues = CatEAccountingStatus::get();

		return response()->json($statues);
	}

	public function getCatOverviews()
	{
		$overviews = CatOverview::get();

		return response()->json($overviews);
	}

	public function updateCompany(StoreAccountingCompanyRequest $request)
	{
		$company = AccountingCompany::where('id', $request->company_id)->update([
						'cat_work_station_id' => $request['work_station_id'],
						'business_name' => $request['company_name'],
						'manager_id' => $request['manager_id'],
						'accountant_id' => $request['accountant_id'],

						'has_law' => $request['has_law'],
						'cat_erp_id' => $request['erp_id'],
					]);

		return response()->json($company);
	}

	public function deleteCompany(Request $request)
	{
		$company = AccountingCompany::where('id', $request->company_id)->delete();

		return response()->json($company);
	}


	public function getCompanies(Request $request){

		$search = isset($request->search) ? $request->search : null;

		$list = AccountingCompany::with(['workStation','manager','accountant'])
						->orWhereRelation('manager',function($q) use ($search){
							return $q->orWhere('name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%");
						})
						->orWhereRelation('accountant',function($q) use ($search){
							return $q->orWhere('name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%");
						})
						->orWhereRaw("(dmiaccg_accounting_companies.business_name like '%$search%')")
						->orderBy('created_at','asc')
						->get();

        return ['success' => 1, 'data' => $list];
	}

	public function getEAccountingGraphic(Request $request)
	{
		$finish_date = Carbon::parse($request->finish_date['value']);

		$electronic_accounting = DmiEAccounting::with(['company'])
			->whereRelation('company', function ($query) use ($request) {
				return $query->where('accountant_id', 'like', "%$request->accountant_id%");
			})
			->where(function ($q) use ($request){
				$q->whereMonth('date', $request->month)->whereYear('date', $request->year);
			})
			->get();

		$in_time = $electronic_accounting->filter( function ($q) use ($finish_date) {
			return $q->created_at <= $finish_date;
		})->count();

		$lately = $electronic_accounting->filter( function ($q) use ($finish_date) {
			return $q->created_at >= $finish_date;
		})->count();

		$data = [$in_time, $lately];

		return $data;
	}
}
