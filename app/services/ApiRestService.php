<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use GuzzleHttp\Client;

class ApiRestService
{
	private $user, $pwd;

	public function __construct()
	{
		$this->user = env('API_PAYROLL_USER');
		$this->pwd = env('API_PAYROLL_PWD');
	}

	public function AuthenticateClient($request)
	{
		$authenticate = new Client([
			'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
			'base_uri' => env('API_ENDPOINT_PAYROLL'),
		]);

		$response = $authenticate->request('POST', '/Usuarios/authenticate', [
						'body' => json_encode([
							"NombreUsuario" => $this->user,
							"Contrasenia" => $this->pwd
						]),
						'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
					]);

		$data = json_decode($response->getBody());

		return $request->type == 1 ? $this->getFile($data->token, $request) : $this->downloadXml($data->token, $request);
	}

	public function getFile(String $token, $request)
	{
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

		$path = "/ReciboNomina/{$request->payroll['company_code']}/{$request->payroll['year']}/{$request->payroll['start_month']}/Nomina {$request->payroll['payroll_code']}/{$request->payroll['rfc']}/PDF";

		$response_data = $payroll_client->request('GET', $path);

		$payroll_data = $response_data->getBody();

		$data = $payroll_data;

		header('Content-Type: application/pdf');

		return base64_encode($data);
	}

	public function downloadXml(String $token, $request)
	{
		$company_code = Auth::user()->personal_intelisis->company_code;

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

		$path = "/ReciboNomina/{$request->payroll['company_code']}/{$request->payroll['year']}/{$request->payroll['start_month']}/Nomina {$request->payroll['payroll_code']}/{$request->payroll['rfc']}/XML";

		$response_data = $payroll_client->request('GET', $path);

		$payroll_data = $response_data->getBody();

		$data = $payroll_data;

		return $data;
	}
}
