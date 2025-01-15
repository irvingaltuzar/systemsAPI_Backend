<?php

namespace App\Http\Controllers;

use App\Services\ApiRestService;
use App\Services\IntelisisSenderService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ToolsController extends Controller
{
	private $intelisisService, $apiRestService;

	public function __construct(IntelisisSenderService $intelisisService, ApiRestService $apiRestService)
	{
		$this->intelisisService = $intelisisService;
		$this->apiRestService = $apiRestService;
	}

    public function getPayroll(int $year,$_rfc = null)
	{
		$payroll = $this->intelisisService->getPayroll($year,$_rfc);

		return response()->json($payroll);
	}

	public function getFilePayroll(Request $request)
	{
		$file = $this->apiRestService->AuthenticateClient($request);

		return $file;
	}

	public function getXmlPayroll(Request $request)
	{
		$file = $this->apiRestService->AuthenticateClient($request);

		return $file;
	}

	public function testPayroll()
	{
		$authenticate = new Client([
			'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
			'base_uri' => env('API_ENDPOINT_PAYROLL'),
		]);

		$response = $authenticate->request('POST', '/Usuarios/authenticate', [
						'body' => json_encode([
							"NombreUsuario" => $this->apiRestService->user,
							"Contrasenia" => $this->apiRestService->pwd
						]),
						'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
					]);

		$data = json_decode($response->getBody());

		return $this->getFile($data->token);
	}

	public function getFile(String $token)
	{
		// $company_code = Auth::user()->personal_intelisis->company_code;

		$payroll_client = new Client([
			'headers' => [
							'Content-Type' => 'application/json',
							'Accept' => 'application/json',
							'Authorization' => 'Bearer ' . $token,
						],
			['debug'   => false],
			'base_uri' => env('API_ENDPOINT_PAYROLL'),
			'timeout' => 2.0
		]);

		$path = "/ReciboNomina/D3002/2023/03/Nomina NCGDL5/NUHK970103261/PDF";

		$response_data = $payroll_client->request('GET', $path);

		$payroll_data = $response_data->getBody();

		$data = $payroll_data;

		header('Content-Type: application/pdf');

		return $data;
	}
}
