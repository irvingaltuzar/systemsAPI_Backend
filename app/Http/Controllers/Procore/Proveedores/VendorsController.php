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
use App\Models\CatTipoAdjunto;
use App\Models\ProcoreConfiguration;
class VendorsController extends Controller

{
    private $procoreAPI;

	public function __construct(ProcoreRepository $procoreAPI)
	{
		$this->procoreAPI = $procoreAPI;
	}
        public function CreateVendor($vendor){
        try {
            $config= ProcoreConfiguration::latest()->first();

            $path = "/rest/v1.0/vendors";

            $response_data = $this->procoreAPI->post( $path, [
                'body' => json_encode([
                    'company_id' => $config->company_id,
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
                $config= ProcoreConfiguration::latest()->first();
                $path = "/rest/v1.3/companies/".$config->company_id."/users";
    
                $response_data =$this->procoreAPI->post( $path, [
                    'body' => json_encode([
                        "user" => [
                            'last_name'=> $vendor->contact,
                            'email_address'=> $vendor->email,
                            'business_phone'=> $vendor->phone,
                            'is_active'=> true,
                            'vendor_id'=> $vendor->procore_id
                        ]
                    ]),
                ]);
            
                return $response_data;
            } catch (Exception $e) {
                // For example, connection issues, timeouts, etc.
                $statusCode = $response->status();
            }
            }

            public function DeleteCompanyFolder($id){
                try {
                    $config= ProcoreConfiguration::latest()->first();
                    $path = "/rest/v1.0/companies/".$config->company_id."/folders/".$id;
        
                    $response_data =$this->procoreAPI->delete($path);
                
                    return $response_data;
                } catch (Exception $e) {
                    // For example, connection issues, timeouts, etc.
                    $statusCode = $response->status();
                }
                }

            public function InviteVendor($id){
                $config= ProcoreConfiguration::latest()->first();
                    $path = "/rest/v1.3/companies/".$config->company_id."/users/".$id."/invite";
        
                    $response = $this->procoreAPI->patch($path);
        
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
            $config= ProcoreConfiguration::latest()->first();
            $response = $this->procoreAPI->postLogin("/oauth/token",[
                "body" => json_encode([
                    "grant_type" => 'authorization_code',
                    "code" => $code,
                    "client_id" =>  $config->client_id,
                    "client_secret" => $config->client_secret,
                    "redirect_uri" => env('APP_URL')."/redirect",
                ])
            ]);

              $this->procoreAPI->addRefreshToken($response);

            return $response;
         }
         public function CreateCompanyFolder($name){
            try {
                $config= ProcoreConfiguration::latest()->first();
                $path = "/rest/v1.0/companies/".$config->company_id."/folders";
    
                $response_data =$this->procoreAPI->post( $path, [
                    'body' => json_encode([
                        "folder" => [
                            "parent_id"=> $config->folder_company_id,
                            "name"=>  $name,
                            // "is_tracked"=>  true,
                            "explicit_permissions"=>  false,
                        ]
                    ]),
                ]);
            
                return $response_data;
            } catch (Exception $e) {
                // For example, connection issues, timeouts, etc.
                $statusCode = $response->status();
            }
            }

         public function fileprocore($name,$idFolder){

         $file = Storage::disk('Proveedores')->path($name);
         $config= ProcoreConfiguration::latest()->first();

        $name=basename($file);
        $ext= pathinfo(storage_path($file), PATHINFO_EXTENSION);
        $mimeType= CatTipoAdjunto::where("extension",".".$ext)->first();

            try {
                $response=  $this->procoreAPI->post("/rest/v1.1/companies/".$config->company_id."/uploads",[
                    'body' => json_encode([
                        "response_filename"=> $name,
                        "response_content_type"=> $mimeType->mimeType,
                    ]),
                    ]
                );
                   $this->uploadfileAmazon($response,$name,$mimeType->mimeType);
                 return $this->relatedFileFolder($name,$response["data"]["uuid"],$idFolder);
                return $response;            

            } catch (Exception $e) {
                // For example, connection issues, timeouts, etc.
                $statusCode = $response->status();
            }
            }

            public function uploadfileAmazon($response,$filename,$mimeType){

                
                $file = Storage::disk("Proveedores")->path($filename);
                $fileExists = Storage::disk('Proveedores')->exists($filename);
                if (!$fileExists) {
                    $file = Storage::disk("EFO")->path($filename);
                }

                try {
                    $response=  $this->procoreAPI->postMultipart($response["data"]["url"],[
                        'multipart' => [
                            [
                                'name' => 'key',
                                'contents' => $response["data"]["fields"]["key"]
                            ],
                            [
                                'name' => 'Content-Type',
                                'contents' => $mimeType
                            ],
                            
                            [
                                'name' => 'Content-Disposition',
                                'contents' => 'inline; filename="'.$filename.'"'
                            ],
                            [
                                'name' => 'policy',
                                'contents' => $response["data"]["fields"]["policy"]
                            ],
                            [
                                'name' => 'x-amz-credential',
                                'contents' => $response["data"]["fields"]["x-amz-credential"]
                            ],
                            [
                                'name' => 'x-amz-algorithm',
                                'contents' => $response["data"]["fields"]["x-amz-algorithm"]
                            ],
                            [
                                'name' => 'x-amz-date',
                                'contents' => $response["data"]["fields"]["x-amz-date"]
                            ],
                            [
                                'name' => 'x-amz-signature',
                                'contents' => $response["data"]["fields"]["x-amz-signature"]
                            ],
                            [
                                'name' => 'file',
                                'contents' => file_get_contents($file),
                                'filename' => $filename // Nombre del archivo
                            ],
                        ]
                    ]
                    );
    
                    return $response;            
    
                } catch (Exception $e) {
                    // For example, connection issues, timeouts, etc.
                    $statusCode = $response->status();
                }
            }

         public function relatedFileFolder($name,$uuid,$idFolder){
            $config= ProcoreConfiguration::latest()->first();
            try {
                $response=  $this->procoreAPI->post("/rest/v1.0/companies/".$config->company_id."/files",[
                    'body' => json_encode([
                        "file" => [
                                'parent_id' => $idFolder,
                                'name' => $name,
                                'upload_uuid' => $uuid
                                // 'data' => $fileContents
                        ],
                        "content_type"=> "application/pdf"
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

}
