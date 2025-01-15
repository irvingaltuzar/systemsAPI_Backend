<?php

namespace App\Http\Controllers\Procore\Proveedores;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DmiabaSupplierRegistration;
use App\Http\Controllers\Procore\Proveedores\VendorsController;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Promise\Promise;
use App\Services\SendEmailService;
use App\Services\IntelisisSenderService;
class SuppliersWebController extends Controller
{
	private $sendEmail, $intelisisService, $vendor;

    public function __construct(VendorsController $vendor,SendEmailService $sendEmail,IntelisisSenderService $intelisisService)
	{
        $this->vendor= $vendor;
		$this->sendEmail = $sendEmail;
		$this->intelisisService = $intelisisService;
	}
    public function approve(Request $request)
	{
		$name = Auth::user()->personal_intelisis->name;
		$last_name = Auth::user()->personal_intelisis->last_name;
    //Send Data information to Procore API
       
		$tmp_supplier = DmiabaSupplierRegistration::with('files')->where('id', $request->id)
							->first();

		if ($request->status == 1) {
			if ($tmp_supplier->status === "0") {

					if($tmp_supplier->user==env("USER_PROCORE")){
						try {
							// // Crear carpeta de la empresa
							$folder = $this->vendor->CreateCompanyFolder($tmp_supplier["rfc"]." - ".$tmp_supplier["business_name"]);
							if ($folder['status'] !== 201) {
								// Manejar el error si la respuesta no es 201

								return response()->json([
									'message' => "Problema al crear la carpeta del proveedor. Msg: ".$folder['data']." Código:".$folder['status'],
								], 400);
							}
							foreach ($tmp_supplier["files"] as $file) {
								// Manejar la creación de archivos en la carpeta
								try {
									$this->vendor->fileprocore($file->name, $folder["data"]["id"]);
								} catch (\Throwable $fileException) {
									$this->vendor->DeleteCompanyFolder($folder["data"]["id"]);

									return response()->json([
										'message' => "Hay un problema con el archivo y no se ha podido cargar: ". $file->name,
									], 400);
									
								}
							}
							// Crear proveedor
							if($tmp_supplier["procore_id"]==null){
							$result = $this->vendor->CreateVendor($tmp_supplier);
							$tmp_supplier->procore_id=$result["data"]["id"];
							$tmp_supplier->save();
							}
						} catch (\GuzzleHttp\Exception\ClientException $folderException) {
						 // Capturar la excepción específica de la creación de la carpeta
							$response = $folderException->getResponse();
							$body = $response->getBody()->getContents();
							$errorData = json_decode($body, true);
							return $errorData;// Por ahora, re-lanzamos la excepción para que sea manejada en un nivel superior
						}
						try {
							// Crear usuario de proveedor
							$promise = $this->vendor->CreateUserVendor($tmp_supplier);
							if ($promise['status'] === 201) {
							
								$this->vendor->InviteVendor($promise["data"]["id"]);
							}
						} catch (\Throwable $createUserVendorException) {
							// Manejar el error de creación del usuario de proveedor
						}
											
						$numIntelisis = null;
					}else{
						$intelisis_active = $this->intelisisService->supplierStatus($tmp_supplier, $request->status, 'null');
						$numIntelisis= $intelisis_active[0]->Proveedor;
					}
					

					
				$this->sendEmail->sendApproveMessage($tmp_supplier, 'approve');


				$supplier = DmiabaSupplierRegistration::where('id', $request->id)
								->update([
									'status' => $request->status,
									'user_approved' => "${name} ${last_name}",
									'referencia_intelisis' => $numIntelisis
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
}
