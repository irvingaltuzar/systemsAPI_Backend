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

class ProcoreRepository
{
	protected $httpClient;
	protected $accessToken;
	protected $refreshToken;

    public function __construct()
    {
		// $this->getTokenInfo();
		// $info = $this->getTokenInfo();
		// if(isset($info)){
			
		// 	if($info["expires_in_seconds"] < 500){
		// 	$this->RefreshTokenPost();
		// 	}
		// }else{
		// 	return response()->json(['error' => "Falta Informacion de Token, ingresa un token valido PROCORE", 'code' => 401], 401); 
		// }

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

        $this->httpClient = new Client([
			'verify' => env('API_PATH_VERIFY_SSL'),
			'headers' => [
						'Content-Type' => 'application/json',
						'Accept' => 'application/json',
						'Authorization' => 'Bearer ' .$refresToken->access_token_id,
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
		// $info = $this->getTokenInfo();
		// if(isset($info)){
			
		// 	if($info["expires_in_seconds"] < 500){
		// 		$this->RefreshTokenPost();
		// 	}
		
		$refresToken= OAuthRefreshTokenProcore::where("revoked",0)->first();
        $this->httpClient = new Client([
			'verify' => env('API_PATH_VERIFY_SSL'),
			'headers' => [
						'Content-Type' => 'application/json',
						'Accept' => 'application/json',
						'Authorization' => 'Bearer ' .$refresToken->access_token_id,

						],
            'base_uri' => env('API_PROCORE_PRUEBAS'), // URL base de la API externa
        ]);
		try {
		$response= $this->httpClient->post($endpoint,$data);
		return json_decode($response->getBody(), true);
		
		}catch (RequestException $e) {
		// if ($e->getResponse() !== null) {
		// 	$statusCode = $e->getResponse()->getStatusCode();
			
		// 	if ($statusCode === 401) {
		// 		$this->RefreshTokenPost();
	
		// 		$response= $this->httpClient->post($endpoint,$data);
	
		// 		return json_decode($response->getBody(), true);

		// 	} else {
		// 		// Otro código de estado de error
		// 		// Manejar el error de acuerdo a tus necesidades
		// 	}
		// } else {
		// 	// Error de conexión u otro tipo de error
		// 	// Manejar el error de acuerdo a tus necesidades
		// }
			}
		// }else{
		// 	return response()->json(['error' => "Falta Informacion de Token, ingresa un token valido PROCORE", 'code' => 401], 401); 
		// }
    }
	public function postLogin($endpoint, $data = [])
    {

        $this->httpClient = new Client([
			'verify' => env('API_PATH_VERIFY_SSL'),
			'headers' => [
						'Content-Type' => 'application/json',
						'Accept' => 'application/json',
						],
            'base_uri' => env('API_PROCORE_LOGIN_PRUEBAS'), // URL base de la API externa
        ]);
				$response= $this->httpClient->post($endpoint,$data);
				$responseData = json_decode($response->getBody(), true);

		return $responseData;
    }

    public function patch($endpoint)
    {
		// $info = $this->getTokenInfo();
		// if(isset($info)){
			
		// 	if($info["expires_in_seconds"] < 500){
		// 		$this->RefreshTokenPost();
		// 	}
		
		$refresToken= OAuthRefreshTokenProcore::where("revoked",0)->first();
        $this->httpClient = new Client([
			'verify' => env('API_PATH_VERIFY_SSL'),
			'headers' => [
						'Content-Type' => 'application/json',
						'Accept' => 'application/json',
						'Authorization' => 'Bearer ' .$refresToken->access_token_id,

						],
            'base_uri' => env('API_PROCORE_PRUEBAS'), // URL base de la API externa
        ]);

		try {
			$response= $this->httpClient->patch($endpoint);
			return json_decode($response->getBody(), true);
			
			}catch (RequestException $e) {

			}
        // return $this->request('PATCH', $endpoint, ['json' => $data],$options);

			// }else{
			// 	return response()->json(['error' => "Falta Informacion de Token, ingresa un token valido PROCORE", 'code' => 401], 401); 
			// }
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
				try {
					$this->httpClient = new Client([
						'verify' => env('API_PATH_VERIFY_SSL'),
						'headers' => [
									'Content-Type' => 'application/json',
									'Accept' => 'application/json',
									'Authorization' => 'Bearer ' .$refresToken->access_token_id,
									],
						'base_uri' => env('API_PROCORE_LOGIN_PRUEBAS'), // URL base de la API externa
					]);
					

							$response= $this->httpClient->post("/oauth/token", [
								"body" => json_encode([
								"grant_type" => 'refresh_token',
								"refresh_token" => $refresToken->refresh_token_id,
								"client_id" =>  env('CLIENT_ID_PROCORE'),
								"client_secret" => env('CLIENT_SECRET_PROCORE'),
								"redirect_uri" => "http://localhost:8000/redirect",
							])
								]);
							$responseData = json_decode($response->getBody(), true);

							$this->addRefreshToken($responseData);
					
					return $responseData;
				} catch (\Throwable $th) {
					throw $th;
				}
					
    }
	public function getTokenInfo()
    {
		$refresToken= OAuthRefreshTokenProcore::where("revoked",0)->first();
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
					'base_uri' => env('API_PROCORE_LOGIN_PRUEBAS'), // URL base de la API externa
				]);
			
							$response= $this->httpClient->get("/oauth/token/info");
							$responseData = json_decode($response->getBody(), true);

							$this->updateAccessToken($responseData);
				
						return $responseData;
						
					}  catch (RequestException $e) {
							if ($e->getResponse() !== null) {
								$statusCode = $e->getResponse()->getStatusCode();
								
								if ($statusCode === 401) {
									$this->RefreshTokenPost();
									
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

		OAuthRefreshTokenProcore::where('revoked',0)->update(['expires_at' =>  $response["expires_in_seconds"]]);

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
