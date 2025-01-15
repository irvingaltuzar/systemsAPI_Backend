<?php

namespace App\Repositories;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;
use App\Models\OAuthTokenProcore;
use App\Models\OAuthRefreshTokenProcore;
use App\Models\ProcoreConfiguration;

class ProcoreRepository
{
	protected $httpClient;
	protected $accessToken;
	protected $refreshToken;

    public function __construct()
    {
		$info = $this->getTokenInfo();

    }
	
    public function setAccessToken($token)
    {
        $this->accessToken = $token;

    }

	public function getAccessToken()
    {
       return $this->accessToken;

    }
	public function setRefreshToken($string)
    {
        $this->refreshToken = $string;

    }


    public function get($endpoint)
    {
	
		$refresToken= OAuthRefreshTokenProcore::where("revoked",0)->first();
		$config= ProcoreConfiguration::latest()->first();

        $this->httpClient = new Client([
			'verify' => env('API_PATH_VERIFY_SSL'),
			'headers' => [
						'Content-Type' => 'application/json',
						'Accept' => 'application/json',
						'Authorization' => 'Bearer ' .$refresToken->access_token_id,
						'Procore-Company-Id' => $config->company_id
						],
            'base_uri' => env('API_PROCORE_PRUEBAS'), // URL base de la API externa
        ]);

		try {
        $response= $this->httpClient->get($endpoint);

		return json_decode($response->getBody(), true);

		}  catch (RequestException $e) {
			return response()->json(['error' => "Ha ocurrido un error al procesar la petición...", 'code' => 400], 400); 
		}
	
    }

    public function post($endpoint, $data = [])
    {
	
		$refresToken= OAuthRefreshTokenProcore::where("revoked",0)->first();
		$config= ProcoreConfiguration::latest()->first();

        $this->httpClient = new Client([
			'verify' => env('API_PATH_VERIFY_SSL'),
			'headers' => [
						'Content-Type' => 'application/json',
						'Accept' => 'application/json',
						'Authorization' => 'Bearer ' .$refresToken->access_token_id,
						'Procore-Company-Id' => $config->company_id
						],
            'base_uri' => $config->service_url, // URL base de la API externa
        ]);
		try {
			$response = $this->httpClient->post($endpoint, $data);
			$responseData = json_decode($response->getBody(), true);
			$statusCode = $response->getStatusCode();
			
			return ['status' => $statusCode, 'data' => $responseData];
		} catch (RequestException $e) {
			// Captura los errores de la solicitud
			if ($e->hasResponse()) {
				// Si la excepción tiene una respuesta, obtén el código de estado
				$statusCode = $e->getResponse()->getStatusCode();
				$errorResponse = json_decode($e->getResponse()->getBody()->getContents(), true);
				$errorMessage = isset($errorResponse['errors']['name']) ? implode(", ", $errorResponse['errors']['name']) : 'Error de solicitud';
				
				return ['status' => $statusCode, 'data' => $errorMessage];
			}
			// Devuelve la excepción para manejarla en el código que llama a este método
			return ['status' => 500, 'error' => $e->getMessage()];
		}
    }
	public function postMultipart($endpoint, $data = [])
    {
	
		$refresToken= OAuthRefreshTokenProcore::where("revoked",0)->first();
		$config= ProcoreConfiguration::latest()->first();
        $this->httpClient = new Client([
			'verify' => env('API_PATH_VERIFY_SSL'),
			'headers' => [
						'Content-Type' => 'multipart/form-data',
						'Procore-Company-Id' => $config->company_id
						// 'Accept' => 'application/json',
						// 'Authorization' => 'Bearer ' .$refresToken->access_token_id,

						],
			'base_uri' => $config->service_url, // URL base de la API externa
        ]);
		try {
		$response= $this->httpClient->post($endpoint,$data);
		return json_decode($response->getBody(), true);
		
		}catch (RequestException $e) {
			return $e;
		if ($e->getResponse() !== null) {
			$statusCode = $e->getResponse()->getStatusCode();
			
			
		}
			}
    }
	public function postLogin($endpoint, $data = [])
    {
		$config= ProcoreConfiguration::latest()->first();
		
        $this->httpClient = new Client([
			'verify' => env('API_PATH_VERIFY_SSL'),
			'headers' => [
						'Content-Type' => 'application/json',
						'Accept' => 'application/json',
						],
            'base_uri' => $config->service_url_login // URL base de la API externa
        ]);
				$response= $this->httpClient->post($endpoint,$data);
				$responseData = json_decode($response->getBody(), true);

		return $responseData;
    }
	public function delete($endpoint)
	{
		$refresToken = OAuthRefreshTokenProcore::where("revoked", 0)->first();
		$config = ProcoreConfiguration::latest()->first();
	
		$this->httpClient = new Client([
			'verify' => env('API_PATH_VERIFY_SSL'),
			'headers' => [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Bearer ' . $refresToken->access_token_id,
				'Procore-Company-Id' => $config->company_id
			],
			'base_uri' => $config->service_url, // URL base de la API externa
		]);
	
		try {
			$response = $this->httpClient->delete($endpoint);
			$responseData = json_decode($response->getBody(), true);
			$statusCode = $response->getStatusCode();
			
			return ['status' => $statusCode, 'data' => $responseData];
		} catch (RequestException $e) {
			// Captura los errores de la solicitud
			if ($e->hasResponse()) {
				// Si la excepción tiene una respuesta, obtén el código de estado y el mensaje de error
				$statusCode = $e->getResponse()->getStatusCode();
				$errorResponse = json_decode($e->getResponse()->getBody()->getContents(), true);
				$errorMessage = isset($errorResponse['errors']['name']) ? implode(", ", $errorResponse['errors']['name']) : 'Error de solicitud';
				
				return ['status' => $statusCode, 'error' => $errorMessage];
			}
			// Devuelve la excepción para manejarla en el código que llama a este método
			return ['status' => 500, 'error' => $e->getMessage()];
		}
	}
    public function patch($endpoint)
    {
	
		$refresToken= OAuthRefreshTokenProcore::where("revoked",0)->first();
		$config= ProcoreConfiguration::latest()->first();
        $this->httpClient = new Client([
			'verify' => env('API_PATH_VERIFY_SSL'),
			'headers' => [
						'Content-Type' => 'application/json',
						'Accept' => 'application/json',
						'Authorization' => 'Bearer ' .$refresToken->access_token_id,
						'Procore-Company-Id' => $config->company_id
						],
			'base_uri' => $config->service_url, // URL base de la API externa
        ]);

		try {
			$response= $this->httpClient->patch($endpoint);
			return json_decode($response->getBody(), true);
			
			}catch (RequestException $e) {

			}
    }

    // Método privado para realizar la solicitud HTTP
    public function request($method, $endpoint, $options)
    {
        try {
            $response = $this->httpClient->request($method, $endpoint, $options);
            return json_decode($response->getBody(), true); // Devuelve la respuesta como array asociativo
        } catch (RequestException $e) {
            // Manejo de excepciones, si es necesario
            // Puedes personalizar el manejo de errores según tus necesidades
            return null;
        }
    }

	public function RefreshTokenPost()
    {
		$refresToken= OAuthRefreshTokenProcore::where("revoked",0)->first();
		$config= ProcoreConfiguration::latest()->first();
				try {
					$this->httpClient = new Client([
						'verify' => env('API_PATH_VERIFY_SSL'),
						'headers' => [
									'Content-Type' => 'application/json',
									'Accept' => 'application/json',
									'Authorization' => 'Bearer ' .$refresToken->access_token_id,
									],
						'base_uri' => $config->service_url, // URL base de la API externa
					]);
					

							$response= $this->httpClient->post("/oauth/token", [
								"body" => json_encode([
								"grant_type" => 'refresh_token',
								"refresh_token" => $refresToken->refresh_token_id,
								"client_id" =>   $config->client_id,
								"client_secret" =>  $config->client_secret,
								"redirect_uri" => env('APP_URL')."/redirect",
							])
								]);
							$responseData = json_decode($response->getBody(), true);

							$this->addRefreshToken($responseData);
					
					return $responseData;
				}  catch (RequestException $e) {
							// Devuelve la excepción para manejarla en el código que llama a este método
					return ['status' => 401, 'data' => "Hubo un problema, token no valido, volver a autenticar con PROCORE"];
				}
					
    }
	public function getTokenInfo()
    {
		$refresToken= OAuthRefreshTokenProcore::where("revoked",0)->first();
		$config= ProcoreConfiguration::latest()->first();
		try {

			if(isset($refresToken)){
				try {

				$this->httpClient = new Client([
					'verify' => env('API_PATH_VERIFY_SSL'),
					'headers' => [
								'Content-Type' => 'application/json',
								'Accept' => 'application/json',
								'Authorization' => 'Bearer ' .$refresToken->access_token_id,
								],
					'base_uri' => $config->service_url_login, // URL base de la API externa
				]);
			
							$response= $this->httpClient->get("/oauth/token/info");
							$responseData = json_decode($response->getBody(), true);

							$this->updateAccessToken($responseData);

							if($responseData["expires_in"] < 500){
									$this->RefreshTokenPost();
							}

						return $responseData;
						
					}  catch (RequestException $e) {
							if ($e->getResponse() !== null) {
								$statusCode = $e->getResponse()->getStatusCode();
								
								if ($statusCode === 401) {
								return $this->RefreshTokenPost();
									
								} else {
									// Otro código de estado de error
									// Manejar el error de acuerdo a tus necesidades
								}
							} else {
								// Error de conexión u otro tipo de error
								// Manejar el error de acuerdo a tus necesidades
							}
						}

					
			}else{
				return null;
			}
			} catch (\Throwable $th) {
				throw $th;
			}
    }
	public function updateAccessToken($response){

		OAuthTokenProcore::where('revoked',0)->update(['resource_owner_id' =>  $response["resource_owner_id"]]);

		OAuthRefreshTokenProcore::where('revoked',0)->update(['expires_at' =>  $response["expires_in"]]);

	}
	public function addCodeAccessToken($code){
		OAuthTokenProcore::query()->update(['revoked' => 1 ]);
		//save token in table oauth_refresh_token
		     
		$token = new OAuthTokenProcore();
		$token->id= $code;
		$token->revoked= 0;
		$token->save();

	}
	public function addRefreshToken($response){
		OAuthRefreshTokenProcore::query()->update(['revoked' => 1 ]);
		//save token in table oauth_refresh_token
		$token = new OAuthRefreshTokenProcore();
		$token->access_token_id= $response["access_token"];
		$token->token_type= $response["token_type"];
		$token->expires_at= $response["expires_in"];
		$token->refresh_token_id= $response["refresh_token"];
		$token->revoked= 0;
		$token->save();

	}
}
