<?php

namespace App\Http\Controllers\Alfa\Dining;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCashoutRequest;
use App\Http\Requests\StoreDiningMenuRequest;
use App\Http\Requests\StoreProductTakedRequest;
use App\Http\Requests\UpdateDiningMenuRequest;
use App\Models\CatFoodType;
use App\Models\DmiRhFoodOfferProduct;
use App\Models\DmiRhFoodOrderProduct;
use App\Models\DmiRhPaymentsLogs;
use App\Models\FoodMenu;
use App\Models\FoodMenuFile;
use App\Models\FoodOrder;
use App\Repositories\DiningMenuRepository;
use App\Repositories\ToolsRepository;
use App\Services\FileUploaderService;
use App\Services\IntelisisSenderService;
use App\Services\SendEmailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PDF;

class DiningController extends Controller
{

	private $toolsRepository, $fileUploader, $diningRepository, $sendEmail;

	public function __construct(ToolsRepository $toolsRepository, FileUploaderService $fileUploader, DiningMenuRepository $diningRepository, SendEmailService $sendEmail)
	{
		$this->toolsRepository = $toolsRepository;
		$this->fileUploader = $fileUploader;
		$this->diningRepository = $diningRepository;
		$this->sendEmail = $sendEmail;
	}

	public function getTools()
	{
		return response()->json($this->toolsRepository->getLocations());
	}

	public function storeMenu(StoreDiningMenuRequest $request)
	{
		$menu = FoodMenu::create([
			'location_id' => $request['locations_id'],
			'enabled_days' => $request['enabled_days'],
			'start_date' => $request['start_date'],
			'finish_date' => $request['finish_date'],
			'general_price' => 0,
			'employee_price' => 55,
			'is_open' => 0
		]);

		if (!!$request['files']) {
			foreach ($request['files'] as $file) {
				$file = $this->fileUploader->upload($file, [
							'type' => 'Comedor',
							'id' => $menu->id
						]);

				$this->storeMenuFile($menu->id, $file['filename']);
			}
		} else {
			false;
		}

		return response()->json($menu);
	}

	public function closeMenu(Request $request)
	{
		$menu = FoodMenu::find($request->id);

		$menu->is_open = !$menu->is_open;
		$menu->save();

		return $menu;
	}

	public function storeOrder(Request $request)
	{

		$food_order = FoodOrder::create([
			'seg_usuario_id' => Auth::user()->usuarioId,
			'food_menu_id' => $request->menu_id,
		]);

		$this->storeOrderProducts($request, $food_order);

		return response()->json($food_order);
	}

	public function getUserCashout()
	{
		return response()->json($this->diningRepository->getCashouts());
	}

	public function getCurrentOrder()
	{
		$now = Carbon::now()->addDays(7);
		$week_start = $now->startOfWeek()->format('Y-m-d');

		$week_end = $now->startOfWeek()->format('Y-m-d');

		$order = FoodOrder::with(['products', 'menu'])
					->whereHas('menu', function ($q) use ($week_start, $week_end) {
						return $q->whereBetween('start_date', [$week_start, $week_end])
									->orWhereBetween('finish_date', [$week_start, $week_end]);
					})
					->where('seg_usuario_id', Auth::user()->usuarioId)
					->first();

		return response()->json($order);
	}

	public function getOrders(Request $request)
	{

        $order_by = isset($request->order_by) ? $request->order_by : 'asc';

        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';

        $search = isset($request->search) ? $request->search : '';


        if(isset($search) && strlen($search) > 0){

            $food_menu = FoodOrder::with(['products' => function ($q) {
								return $q->where('offered', 0);
							}, 'menu'])
							->whereHas('menu', function ($q) use ($search) {
								return $q->whereRaw("(start_date like '%$search%')");
							})
							->where('seg_usuario_id', Auth::user()->usuarioId)
							->orderBy('created_at', $order_by)
							->Paginate($limit);

        }else{

            $food_menu = FoodOrder::with(['products' => function ($q) {
								return $q->where('offered', 0);
							}, 'menu'])
							->orderBy('created_at',$order_by)
							->where('seg_usuario_id', Auth::user()->usuarioId)
							->Paginate($limit);

        }

        $food_menu->setPath('/dining/order/get-all-orders');

        return response()->json($food_menu);
	}

	public function storeOrderProducts(Request $request, FoodOrder $food_order)
	{
		foreach ($request['order'] as $key => $order) {
			DmiRhFoodOrderProduct::create([
				'food_order_id' => $food_order->id,
				'food_type_id' => $order['type'],
				'work_day_id' => $order['day'],
			]);
		}
	}

	public function storeOffer(Request $request)
	{
		foreach ($request->product as $key => $product_offered) {
			$founded = DmiRhFoodOrderProduct::where('id', $product_offered['product'])->update([
				'offered' => 1
			]);

			$offer = DmiRhFoodOfferProduct::create([
				'food_order_product_id' => $product_offered['product']
			]);
		}

		return response()->json($offer);
	}

	public function getWeekMenu()
	{
		$now = Carbon::now()->addDays(7);
		$week_start = $now->startOfWeek()->format('Y-m-d');

		$week_end = $now->startOfWeek()->format('Y-m-d');

		$week_menu = FoodMenu::whereBetween('start_date', [$week_start, $week_end])
						->orWhereBetween('finish_date', [$week_start, $week_end])
						->first();

		return response()->json($week_menu);
	}

	public function getCatFoodType()
	{
		$cat_food = CatFoodType::get();

		return response()->json($cat_food);
	}

	public function takeProduct(StoreProductTakedRequest $request)
	{
		$has_product = $this->diningRepository->checkCurrentOrders();

		if ($has_product) {
			return $has_product;
		} else {

			$tmp_order = $this->diningRepository->storeOrder($request->findObject['product']['order']['food_menu_id']);

			$product = $this->diningRepository->storeProduct($request, $tmp_order);

			$this->diningRepository->updateOffered($request->findObject['food_order_product_id']);
		}
	}

	public function updateMenu(UpdateDiningMenuRequest $request)
	{

		$enabled_days_serialized = serialize($request['enabled_days']);

		$menu = FoodMenu::where('id', $request['menu_id'])->update([
			'location_id' => $request['locations_id'],
			'enabled_days' => $enabled_days_serialized,
			'start_date' => $request['start_date'],
			'finish_date' => $request['finish_date'],
		]);

		if (!!$request['file_updated']) {
			foreach ($request['new_files'] as $file) {
				$file = $this->fileUploader->upload($file, [
							'type' => 'Comedor',
							'id' => $request['menu_id']
						]);

				$this->updateMenuFile($request['menu_id'], $file['filename']);
			}

			$path = "Comedor/{$request['menu_id']}/{$request['files']['file']}";

			Storage::disk('Publico')->delete($path);
		} else {
			return false;
		}

		return response()->json($menu);
	}

	public function fetchMenu(Request $request)
	{
        $order_by = isset($request->order_by) ? $request->order_by : 'asc';

        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';

        $search = isset($request->search) ? $request->search : '';


        if(isset($search) && strlen($search) > 0){

            $food_menu = FoodMenu::with(['image'])->whereRaw("(start_date like '%$search%'
                or created_at like '%$search%'
            )")
			// ->where('seg_usuario_id', Auth::user()->usuarioId)
            ->orderBy('start_date',$order_by)
			->Paginate($limit);

        }else{

            $food_menu = FoodMenu::with(['image'])
							// ->where('seg_usuario_id', Auth::user()->usuarioId)
							->orderBy('start_date',$order_by)
							->Paginate($limit);

        }

        $food_menu->setPath('/dining/fetch');

        return $food_menu;
	}

	public function uploadCashout(StoreCashoutRequest $request)
	{
		$food_menu = $this->diningRepository->getTotalOrders($request['start_date'], $request['finish_date']);

		$intelisis_payload = $this->diningRepository->storeDataIntelisis($food_menu, $request);

		return response()->json($intelisis_payload);
	}

	public function storeMenuFile(int $menu_id, String $filename)
	{
		FoodMenuFile::create([
			'food_menu_id' => $menu_id,
			'file_menu' => $filename
		]);
	}

	public function updateMenuFile(int $menu_id, String $filename)
	{
		FoodMenuFile::where('food_menu_id', $menu_id)->update([
			'file_menu' => $filename
		]);
	}

	public function getOfferProducts()
	{
		$now = Carbon::now();

		$week_start = $now->startOfWeek()->format('Y-m-d');

		$day_name = Carbon::now()->dayName;

		$offers = DmiRhFoodOfferProduct::with(['product.order.menu'])
					->whereHas('product.workDay', function ($q) use($day_name) {
						return $q->where('description', 'like', "%$day_name%");
					})
					->whereHas('product.order.menu',function ($query) use ($week_start) {
						return $query->where('start_date', 'like', "%$week_start%");
					})
					->get()
					->groupBy([fn ($query) => $query->product->food_type]);

		return response()->json($offers);
	}

	public function getPdf(int $id)
	{
		$data = FoodMenu::with(['orders.products'])->where('id', 19)->first();


		$pdf = PDF::loadView('pdf.dining.show',  [
				'data' => $data,
				'id' => $data->id,
				'date' => Carbon::now()->translatedFormat('j M Y')
			], [
				'format' => 'A4',
				'orientation' => 'L'
			]);

		return $pdf->stream();
	}

	public function testPDF(int $id)
	{
		$data = FoodMenu::with(['orders.products'])->where('id', $id)->first();

		$pdf = PDF::loadView('pdf.dining.show',  [
				'data' => $data,
				'id' => $data->id,
				'date' => Carbon::now()->translatedFormat('j M Y')
			], [], [
				'format' => 'A4',
				'orientation' => 'L',
				'margin_header' => 1,
			]);

		return $pdf->stream();
	}

	public function getCurrentProduct()
	{
		$has_product = $this->diningRepository->getCurrentProduct();

		return response()->json($has_product);
	}

	public function fetchPayments(Request $request)
	{
        $order_by = isset($request->order_by) ? $request->order_by : 'asc';

        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';

        $search = isset($request->search) ? $request->search : '';


        if(isset($search) && strlen($search) > 0){

            $payments = DmiRhPaymentsLogs::with('responsable')->whereRaw("(start_date like '%$search%'
                or created_at like '%$search%'
            )")
			// ->where('seg_usuario_id', Auth::user()->usuarioId)
            ->orderBy('start_date',$order_by)
			->Paginate($limit);

        }else{

            $payments = DmiRhPaymentsLogs::with('responsable')->orderBy('start_date',$order_by)
							->Paginate($limit);

        }

        $payments->setPath('/dining/fetch');

        return $payments;
	}

	public function test()
	{
		return $this->sendEmail->test();
	}
}
