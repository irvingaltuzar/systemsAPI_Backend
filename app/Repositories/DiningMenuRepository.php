<?php

namespace App\Repositories;

use App\Services\IntelisisSenderService;
use App\Models\DmiRhPaymentsLogsDetail;
use App\Models\DmiRhFoodOfferProduct;
use App\Models\DmiRhFoodOrderProduct;
use Illuminate\Support\Facades\Auth;
use App\Models\DmiRhPaymentsLogs;
use Illuminate\Http\Request;
use App\Models\FoodOrder;
use App\Models\FoodMenu;
use App\Models\Location;
use Carbon\Carbon;
class DiningMenuRepository
{

	private $intelisisService;

	public function __construct(IntelisisSenderService $intelisisService)
	{
		$this->intelisisService = $intelisisService;
	}

	public function checkCurrentOrders()
	{
		$now = Carbon::now();

		$week_start = $now->startOfWeek()->format('Y-m-d');


		$food_menu = FoodOrder::whereHas('menu', function ($q) use ($week_start) {
			return $q->whereRaw("(start_date like '%$week_start%')");
		})
		->where('seg_usuario_id', Auth::user()->usuarioId)
		->first();

		return $food_menu;
	}

	public function storeOrder(int $food_menu_id)
	{
		$tmp_order = FoodOrder::create([
			'seg_usuario_id' => Auth::user()->usuarioId,
			'food_menu_id' => $food_menu_id
		]);

		return $tmp_order;
	}

	public function storeProduct(Request $request, FoodOrder $order)
	{
		return DmiRhFoodOrderProduct::create([
			'food_order_id' => $order->id,
			'food_type_id' => $request->findObject['product']['food_type_id'],
			'work_day_id' => $request->findObject['product']['work_day_id'],
			'bought' => 1
		]);
	}

	public function updateOffered(int $product_id)
	{
		DmiRhFoodOfferProduct::where('food_order_product_id', $product_id)->update([
			'user_buyer_id' => Auth::user()->usuarioId
		]);

		return DmiRhFoodOrderProduct::where('id', $product_id)->update([
			'taked' => 1
		]);
	}

	public function getCurrentProduct()
	{
		$now = Carbon::now();

		$week_start = $now->startOfWeek()->format('Y-m-d');

		$day_name = Carbon::now()->dayName;

		$offers = DmiRhFoodOrderProduct::with(['order'])
					->whereHas('workDay', function ($q) use($day_name) {
						return $q->where('description', 'like', "%$day_name%");
					})
					->whereHas('order.menu',function ($query) use ($week_start) {
						return $query->where('start_date', 'like', "%$week_start%");
					})
					->WhereHas('order',function ($query) use ($week_start) {
						return $query->where('seg_usuario_id', Auth::user()->usuarioId);
					})
					->first();

		return $offers ? true : false;
	}

	public function getTotalOrders(String $start_date, String $finish_date)
	{
		return FoodMenu::withCount('orders')->with(['orders' => function ($query){
					return $query->withCount(['products' => function ($q) {
						return $q->where('taked', '<>', true)->where('offered', '<>', true);
					}]);
				}])
				->whereBetween('start_date', [$start_date, $finish_date])
				->whereBetween('finish_date', [$start_date, $finish_date])
				->get();
	}

	public function storeDataIntelisis($food_menu, Request $request)
	{
		$account = Carbon::now()->format('Ymd');

		$dining_log = $this->storeDiningLog($request['start_date'], $request['finish_date'], $request['payment_day'], $account);

		foreach ($food_menu as $menu) {
			foreach ($menu->orders as $order) {
				$amount = $order->products_count * $menu->employee_price;

				$this->intelisisService->store([
					'rama' => 'COM',
					'empresa' => $order->user->personal_intelisis->company_code,
					'sucursal' => $order->user->personal_intelisis->branch_code,
					'subcuenta' => $order->user->personal_intelisis->personal_id,
					'cargos' =>	$amount,
					'abonos' => null,
					'Cuenta' => $account,
					'cargos_u' => 1,
					'abonos_u' => null,
					'observaciones' => "Cantidad de productos cobrados: $order->products_count"
				]);

				$this->storeDiningLogDetail($order, $amount, $dining_log->id);
			}
		}

		$token = $this->intelisisService->excecSp('COM', $account);

		return $this->setTokenSp($token, $account);
	}

	public function storeDiningLog(String $start_date, String $finish_date, $payment_day, $account)
	{
		return DmiRhPaymentsLogs::create([
			'start_date' => $start_date,
			'finish_date' => $finish_date,
			'sp_token' => 'NA',
			'seg_usuario_id' => Auth::user()->usuarioId,
			'account_code' => $account,
			'salary_paid' => $payment_day,
		]);
	}

	public function storeDiningLogDetail($order, $amount, $payment_log_id)
	{
		DmiRhPaymentsLogsDetail::create([
			'payment_log_id' => $payment_log_id,
			'personal_intelisis_id' => $order->user->personal_intelisis->id,
			'food_order_id' => $order->id,
			'amount' => $amount,
			'products' => $order->products_count,
		]);
	}

	public function setTokenSp($token, $account)
	{
		return DmiRhPaymentsLogs::where('account_code', $account)->update([
			'sp_token' => $token
		]);
	}

	public function getCashouts()
	{
		$payment_log = DmiRhPaymentsLogs::with(['responsable', 'detail' => function ($q) {
						return $q->where('personal_intelisis_id', Auth::user()->personal_intelisis->id);
					}])
					->latest()
					->first();

		if (!!$payment_log) {

			$cash_out = collect($payment_log);

			$cash_out->put('amount', $payment_log->detail->sum(fn ($log) => $log->amount));

		} else {
			return false;
		}
	}
}
