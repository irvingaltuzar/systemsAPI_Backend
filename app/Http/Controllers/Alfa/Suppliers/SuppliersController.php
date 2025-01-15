<?php

namespace App\Http\Controllers\Alfa\Suppliers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CancelSupplierRequest;
use App\Http\Requests\StoreSpecialtyRequest;
use App\Http\Requests\StoreSupplierDownRequest;
use App\Http\Requests\StoreSupplierSpecialtyRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\CatSupplierSpecialty;
use App\Models\CatDocumentSupplier;
use App\Models\DmiabaSupplierRegistration;
use App\Models\SupplierSpecialty;
use App\Services\IntelisisSenderService;
use App\Services\SendEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use App\Models\DmiabaDocumentsSupplier;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Promise\Promise;

class SuppliersController extends Controller
{
	 private $sendEmail, $intelisisService, $vendor;

	public function __construct(SendEmailService $sendEmail, IntelisisSenderService $intelisisService)
	{
		$this->sendEmail = $sendEmail;
		$this->intelisisService = $intelisisService;
	}

	public function getTotalData()
	{
		$suppliers = DmiabaSupplierRegistration::get();

		$specialties = CatSupplierSpecialty::get();

		$success = $suppliers->filter(function ($q) {
			return $q->status == 1;
		})->values()->all();

		$stand_by = $suppliers->filter(function ($q) {
			return $q->status === "0";
		})->values()->all();

		$blocked = $suppliers->filter(function ($q) {
			return $q->status == 2;
		})->values()->all();

		return response()->json([
			'total_stand_by' =>	[
				'name' => 'En proceso',
				'value' => count($stand_by),
				'icon' => 'history',
				'color' => 'orange',
				'order' => 0
			],
			'total_successs' => [
				'name' => 'Aprobados',
				'value' => count($success),
				'icon' => 'verified',
				'color' => 'green',
				'order' => 1
			],
			'total_blocked' => [
				'name' => 'Bajas',
				'value' => count($blocked),
				'icon' => 'highlight_off',
				'color' => 'red',
				'order' => 2
			],
			'specialties' => [
				'name' => 'Especialidades',
				'value' => count($specialties),
				'icon' => 'star',
				'color' => 'blue',
				'order' => 4
			],
		]);
	}

	public function fetchSuppliers(Request $request)
	{
        $order_by = isset($request->order_by) ? $request->order_by : 'asc';

        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';

        $search = isset($request->search) ? $request->search : '';


        if(isset($search) && strlen($search) > 0){

			$suppliers = DmiabaSupplierRegistration::with(['files', 'specialities', 'logs.seg_auditorium'])
							->where('status', $request->status)
							->where(
								function ($q) use ($search){
									return $q->where('business_name', 'LIKE', '%'.$search.'%')
												->orWhere('rfc', 'LIKE', '%'.$search.'%')
												->orWhere('id', 'LIKE', '%'.$search.'%')
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
												->orWhere('classification', 'LIKE', '%'.$search.'%');
								}
							)
							->orWhere(
								function ($q) use ($search, $request){
									return $q->WhereRelation('responsable',function($q) use ($search){
										return $q->orWhere('name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%");
									})->where('status', $request->status);
								}
							)
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

	public function fetchAllSuppliers(Request $request)
	{
        $order_by = isset($request->order_by) ? $request->order_by : 'asc';

        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';

        $search = isset($request->search) ? $request->search : '';


        if(isset($search) && strlen($search) > 0){

			$suppliers = DmiabaSupplierRegistration::with(['files', 'specialities', 'logs.seg_auditorium'])
							->where('status', $request->status)
							->where(
								function ($q) use ($search){
									return $q->where('business_name', 'LIKE', '%'.$search.'%')
												->orWhere('rfc', 'LIKE', '%'.$search.'%')
												->orWhere('id', 'LIKE', '%'.$search.'%')
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
												->orWhere('classification', 'LIKE', '%'.$search.'%');
								}
							)
							->orWhere(
								function ($q) use ($search, $request){
									return $q->WhereRelation('responsable',function($q) use ($search){
										return $q->orWhere('name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%");
									})->where('status', $request->status);
								}
							)
							->orderBy('created_at', 'desc')
							->Paginate(10);
        }else{

            $suppliers = DmiabaSupplierRegistration::with(['specialities'])
							->where('status', $request->status)
							->orderBy('created_at',$order_by)
							->Paginate($limit);

        }

        $suppliers->setPath('/suppliers/fetch');

        return $suppliers;
	}

	public function exportExcel()
	{
		$suppliers = DmiabaSupplierRegistration::where('status', 1)
						->lazy();

		return response()->json($suppliers);
	}

	public function testPDF(int $id)
	{
		$data = DmiabaSupplierRegistration::with(['files', 'specialities', 'logs.seg_auditorium', 'supplier_bank'])
				->find($id);

		$specialties = $data->specialities;

		$pdf = PDF::loadView('pdf.DMI.suppliers.show',  [
			'data' => $data,
			'id' => $data->id,
			'date' => Carbon::now()->translatedFormat('j M Y'),
			'specialties' => $specialties
			], [], [
				'format' => 'A4',
			]);

		return $pdf->stream();


		$filename = $data->id.'-'.time().'_'.date('Y-m-d').'.pdf';

		$file = $this->storeFile($data->id, $filename);

		$pdf->save(public_path("/storage/pdf/work_permit/{$data->user->userSec->usuario}/{$filename}"));

		return response()->json([
			'url' => Storage::disk('public')->url("work_permit/{{$data->user->userSec->usuario}/{$filename}")
		]);
	}

	public function fetchSpecialties(Request $request)
	{
        $order_by = isset($request->order_by) ? $request->order_by : 'asc';

        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';

        $search = isset($request->search) ? $request->search : '';


        if(isset($search) && strlen($search) > 0){

            $specialties = CatSupplierSpecialty::where('description', 'like', "%$search%")
							->orderBy('description')
							->Paginate($limit);

        }else{
            $specialties = CatSupplierSpecialty::orderBy('description')
							->Paginate($limit);
        }

        $specialties->setPath('/suppliers/fetch-specialties');

        return $specialties;
	}

	// public function approve(Request $request)
	// {
	// 	$name = Auth::user()->personal_intelisis->name;
	// 	$last_name = Auth::user()->personal_intelisis->last_name;
    // //Send Data information to Procore API
       
	// 	$tmp_supplier = DmiabaSupplierRegistration::where('id', $request->id)
	// 						->first();

	// 	if ($request->status == 1) {
	// 		if ($tmp_supplier->status === "0") {
	// 			try {
	// 				$result= $this->vendor->CreateVendor($tmp_supplier);
	// 				$promise= $this->vendor->CreateUserVendor($tmp_supplier);
	// 				$this->vendor->InviteVendor($promise["id"]);
	// 			} catch (\Throwable $th) {
	// 				throw $th;
	// 			}
	// 			// $this->sendEmail->sendApproveMessage($tmp_supplier, 'approve');

	// 			// $intelisis_active = $this->intelisisService->supplierStatus($tmp_supplier, $request->status, 'null');

	// 			$supplier = DmiabaSupplierRegistration::where('id', $request->id)
	// 							->update([
	// 								'status' => $request->status,
	// 								'user_approved' => "${name} ${last_name}",
	// 								// 'referencia_intelisis' => $intelisis_active[0]->Proveedor
	// 							]);

	// 				return response()->json([
	// 					'supplier' => $supplier,
	// 					'message' => 'Proveedor enviado a Procore correctamente',
	// 					'success' => 1
	// 				]);
	// 		} else {
	// 			return response()->json([
	// 				'message' => 'Error al aprobar, proveedor previamente aprobado, actualice su tabla.',
	// 				'success' => 0
	// 			], 500);
	// 		}
	// 	}
	// }

	public function approve_Intelisis(Request $request)
	{
		$name = Auth::user()->personal_intelisis->name;
		$last_name = Auth::user()->personal_intelisis->last_name;

		$tmp_supplier = DmiabaSupplierRegistration::where('id', $request->id)
							->first();

		if ($request->status == 1) {
			if ($tmp_supplier->status === "0") {

				$this->sendEmail->sendApproveMessage($tmp_supplier, 'approve');

				$intelisis_active = $this->intelisisService->supplierStatus($tmp_supplier, $request->status, 'null');

				$supplier = DmiabaSupplierRegistration::where('id', $request->id)
								->update([
									'status' => $request->status,
									'user_approved' => "${name} ${last_name}",
									'referencia_intelisis' => $intelisis_active[0]->Proveedor
								]);

					return response()->json([
						'supplier' => $supplier,
						'message' => 'Proveedor aprobado correctamente',
						'success' => 1
					]);
			} else {
				return response()->json([
					'message' => 'Error al aprobar, proveedor previamente aprobado, actualice su tabla.',
					'success' => 0
				], 500);
			}
		}
	}

	public function cancel(CancelSupplierRequest $request)
	{

		$this->sendEmail->sendCancelMessage($request, 'cancel');

		$canceled = DmiabaSupplierRegistration::where('id', $request->supplier['id'])
						->update([
							'update_user' => 1,
						]);

		return response()->json($canceled);
	}

	public function reactive(Request $request)
	{
		$updated = DmiabaSupplierRegistration::where('id', $request->id)
						->update([
							'status' => $request->erp_id != null ? 1 : 0,
							'update_user' => $request->erp_id != null ? 0 : 1
						]);

		return response()->json($updated);
	}

	public function changeType(Request $request)
	{
		$supplier = DmiabaSupplierRegistration::where('id', $request->id)
						->update([
							'type_supplier' => $request->type
						]);

		return response()->json($supplier);
	}

	public function deleteSpecialty(Request $request)
	{
		$specialty = CatSupplierSpecialty::where('id', $request->specialty_id)->delete();

		return response()->json($specialty);
	}

	public function fecthCatSpecialties()
	{
		$specialties = CatSupplierSpecialty::orderBy('description')->get();

		return response()->json($specialties);
	}

	public function storeSpecialties(StoreSupplierSpecialtyRequest $request)
	{

		$suppliers = SupplierSpecialty::where('supplier_id', $request->supplier_id)->get();

		$founded = $suppliers->whereNotIn('cat_supplier_specialty', $request['array_specialties'])->values()->all();

		if (sizeof($founded) > 0) {
			foreach ($founded as $value) {
				SupplierSpecialty::where('id', $value->id)->delete();
			}

			foreach ($request['array_specialties'] as $specialty) {
				$data = SupplierSpecialty::updateOrCreate([
					'supplier_id' => $request->supplier_id,
					'cat_supplier_specialty' => $specialty
				]);
			}
		} else {
			foreach ($request['array_specialties'] as $specialty) {
				$data = SupplierSpecialty::updateOrCreate([
					'supplier_id' => $request->supplier_id,
					'cat_supplier_specialty' => $specialty
				]);
			}
		}

		return response()->json($data);
	}

	public function storeSpecialty(StoreSpecialtyRequest $request)
	{
		$specialty = CatSupplierSpecialty::create([
			'description' => $request['description']
		]);

		return response()->json($specialty);
	}
	public function getBackSupplier(Request $request)
	{

		$supplier = DmiabaSupplierRegistration::where('id', $request['id'])
						->update([
							'update_user' => null,
						]);

		return response()->json(['success'=>'Actualizado con éxito.'],200);
	}


	public function update(UpdateSupplierRequest $request)
	{

		$supplier = DmiabaSupplierRegistration::where('id', $request['supplier_id'])
						->update([
							'business_name' => $request['business_name'],
							'type_person' => $request['type_person'],
							'rfc' => $request['rfc'],
							'email' => $request['email'],
							'contact' => $request['contact'],
							'address' => $request['address'],
							'phone' => $request['phone'],
							'suburb' => $request['suburb'],
							'cp' => $request['cp'],
							'country' => $request['country'],
							'state' => $request['state'],
							'city' => $request['city'],
							'web_page' => $request['web_page'],
							'bank' => $request['bank'],
							'bank_account' => $request['bank_account'],
							'bank_clabe' => $request['bank_clabe'],
							'credit_days' => $request['credit_days'],
							'currency' => $request['currency'],
						]);


		// Se verifica si hay documentos por cargar
		if(isset($request->uploaded_document_ids)){
			$uploaded_document_ids = explode(",", $request->uploaded_document_ids);

			if(sizeof($uploaded_document_ids) > 0){
				foreach($uploaded_document_ids as $key => $document_id){

					// Se verifica que existe el archivo cargado
					if(isset($request['doc_edit_'.$document_id])){
						$file = $request->file('doc_edit_'.$document_id);

						$original_ext = $file->getClientOriginalExtension();
						$file_name= $request['supplier_id'].'_'.$document_id.'_'.time().'_proveedor.'.$original_ext;
						$doc= new DmiabaDocumentsSupplier();

						if( Storage::disk("Proveedores")->putFileAs("/", $file, $file_name)){
							$doc->name=$file_name;
							$url= Storage::disk("Proveedores")->url($file_name);
							$doc->url= $url;
							$doc->cat_document_supplier_id = $document_id;
							$doc->dmiaba_supplier_registration_id= $request['supplier_id'];
							$doc->save();
						  }

					}
				}
			}
		}

		return response()->json($supplier);
	}

	public function remove(StoreSupplierDownRequest $request)
	{
		$tmp_supplier = DmiabaSupplierRegistration::where('id', $request->id)
							->first();

		if ($tmp_supplier->status === "0" || $tmp_supplier->status === "1") {

			$this->sendEmail->sendRemoveSuppierMessage($tmp_supplier, 'remove', $request['comment']);

			$supplier_code = $tmp_supplier->status == "0" ? 'null' : $tmp_supplier->referencia_intelisis;

			$intelisis_active = $this->intelisisService->supplierStatus($tmp_supplier, 2, $supplier_code);

			$supplier = DmiabaSupplierRegistration::where('id', $request->id)
							->update([
								'status' => $request->status,
								'motive_down' => $request['comment']
							]);

				return response()->json([
					'supplier' => $supplier,
					'message' => 'Proveedor dado de baja correctamente',
					'success' => 1
				]);
		} else {
			return response()->json([
				'message' => 'Error al dar de baja al proveedor, proveedor previamente dado de baja, actualice su tabla.',
				'success' => 0
			], 500);
		}
	}

	public function getSupplierDocuments(){

		$cat_supplier_document = CatDocumentSupplier::all();

		return response()->json(["success" => 1, 'data'=>$cat_supplier_document]);


	}

	public function deleteSupplierDocument($_document_id){

		$deleteRecord = DmiabaDocumentsSupplier::where('id',$_document_id)->first();
		\File::delete("storage/Proveedores/$deleteRecord->name");
		$deleteRecord->delete();

		if($deleteRecord != null){
			return ['success' => 1, 'message' => "Documento eliminado."];
		}else{
			return ['success' => 0, 'message' => "El documento no ha sido eliminado."];
		}

	}


}
