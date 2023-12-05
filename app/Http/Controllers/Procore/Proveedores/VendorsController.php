<?php

namespace App\Http\Controllers\Procore\Proveedores;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Post\PostFile;
use App\Repositories\ProcoreRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Query\Builder;
use App\Models\OAuthTokenProcore;
use App\Models\OAuthRefreshTokenProcore;

class VendorsController extends Controller

{
    private $procoreAPI;

	public function __construct(ProcoreRepository $procoreAPI)
	{
		$this->procoreAPI = $procoreAPI;
	}
        public function CreateVendor($vendor){
        try {
            $path = "/rest/v1.0/vendors";

            $response_data = $this->procoreAPI->post( $path, [
                'body' => json_encode([
                    'company_id' => 4266708,
                    "vendor" => [
                        'name'=> $vendor->business_name,
                        'address'=> $vendor->address." ".$vendor->suburb,
                        'city'=> $vendor->city,
                        'zip'=> $vendor->cp,
                        'business_phone'=> $vendor->phone,
                        'email_address'=> $vendor->email,
                        'is_active'=> true,
                        'website'=> $vendor->web_page,
                        'notes'=> $vendor->rfc,
                    ]
                ]),
            ]);

            return $response_data;
        } catch (Exception $e) {
            $statusCode = $response->status();
        }
        }

        public function CreateUserVendor($vendor){
            try {
             
                $path = "/rest/v1.0/companies/4266708/users";
    
                $response_data =$this->procoreAPI->post( $path, [
                    'body' => json_encode([
                        "user" => [
                            'last_name'=> $vendor->contact,
                            'email_address'=> $vendor->email,
                            'business_phone'=> $vendor->phone,
                            'is_active'=> true
                        ]
                    ]),
                ]);
            
                return $response_data;
            } catch (Exception $e) {
                // For example, connection issues, timeouts, etc.
                $statusCode = $response->status();
            }
            }

            public function InviteVendor($id){
                
                    $path = "/rest/v1.3/companies/4266708/users/".$id."/invite";
        
                    $response = $this->procoreAPI->patch($path);
                    // $payroll_data = $response_data->getBody();
                    // // Convert the JSON response to an associative array
                    //     $responseData = json_decode($payroll_data, true);
        
                    return $response;
              
         }

        public function callback(Request $request)
        {
            $code = $request->input('code');
    
            // Ahora $code contiene el valor del código de autorización
           $id_token= OAuthTokenProcore::find($code);
            if($id_token=="" || $id_token==null){
        
                $this->procoreAPI->addCodeAccessToken($code);
            }
           $response =  $this->newAccessToken($code);
            
             return $response;
           
        }


         public function newAccessToken($code){

            $response = $this->procoreAPI->postLogin("/oauth/token",[
                "body" => json_encode([
                    "grant_type" => 'authorization_code',
                    "code" => $code,
                    "client_id" =>  env('CLIENT_ID_PROCORE'),
                    "client_secret" => env('CLIENT_SECRET_PROCORE'),
                    "redirect_uri" => "http://localhost:8000/redirect",
                ])
            ]);

              $this->procoreAPI->addRefreshToken($response);

            return $response;
         }

         public function fileprocore(){
            // $response = $this->procoreAPI->post("/oauth/token",[
            //     "grant_type" => "client_credentials",
            //     "client_id" =>  env('CLIENT_ID_PROCORE'),
            //     "client_secret" => env('CLIENT_SECRET_PROCORE'),
            // ]);
            //  $this->procoreAPI->refreshToken();
            // $arr=array();
            // $arr=['company_id' =>4266708];
            $file = Storage::disk("Proveedores")->path('intranet.pdf');
           
            $result = [];
            $fileContents = base64_encode(file_get_contents($file));

            mb_parse_str($fileContents, $result);
        //    return $result;
            // $fileContent = base64_encode(file_get_contents($file));
            try {
                $response=  $this->procoreAPI->post("/rest/v1.0/companies/4266708/files",[
                    'body' => json_encode([
                        "file" => [
                                'parent_id' => 11906880,
                                'name' => 'sdfv.pdf',
                                'data' => $fileContents
                            ]
                        ]),
                    ]
                );

                return $response;            

            } catch (\Throwable $th) {
                throw $th;
            }           
                    
         }

         public function me(){
            try {
               
                $response = $this->procoreAPI->get("/rest/v1.0/companies");
    
    
                return $response;
            } catch (Exception $e) {
                // Handle any exceptions that occur during the request
                // For example, connection issues, timeouts, etc.
                $statusCode = $response->status();
                // Customize error handling according to your needs
            }
        }
         public function RefreshToken(){

            $response = $this->procoreAPI->RefreshTokenPost();

            return $response;
         }

         public function getInfoToken(){

            $response = $this->procoreAPI->getTokenInfo();

            return $response;
         }


        //  public function test1(){
        //     try {
        //         $results = DB::connection('erp_dmi_sqlsrv')->select("SET NOCOUNT ON; EXEC spAPIValidaDatos 'PROCORE','PROVEEDOR','ISH2006022Q1'");
        //         dd($results);
        //     } catch (\Exception $e) {
        //         dd($e->getMessage());
        //     }
        //  }
}
