<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use DateTime;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use PDF;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Psr7\Request as GuzzleHttp_Request;
use GuzzleHttp\Utils;
use App\Models\WhatsAppMessage;
use App\Http\Controllers\ToolsController;
use Exception;


class WhatsAppService
{

    private $user_access_token,$recipient_phone_number,$phone_number_id_dmi,$waba_id,$version;
    private $ToolsController,$token_webhook_meta;

    public function __construct(ToolsController $ToolsController){
        $this->user_access_token = env("USER_ACCESS_TOKEN");
        $this->phone_number_id_dmi = env("PHONE_NUMBER_ID_DMI");
        $this->waba_id = env("WABA_ID");
        $this->version= env("VERSION");
        $this->token_webhook_meta= env("TOKEN_WEBHOOK_FACEBOOK");
        
        $this->ToolsController = $ToolsController;
        
    }

    //Se envia un mensaje de tipo texto
    public function sendTextMessage(Request $request){

        try{
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer ".$this->user_access_token,
                    'Cookie' => 'ps_l=0; ps_n=0'
                ],
                'base_uri' => "https://graph.facebook.com/".$this->version,
            ]);
    
            
            $response = $client->request('POST', "/".$this->phone_number_id_dmi."/messages", [
                            'body' => json_encode([
                                "messaging_product"=> "whatsapp",
                                "recipient_type"=> "individual",
                                "to"=> $request->recipient_phone_number,
                                "type"=> "text",
                                "text"=> [
                                            "preview_url"=> false,
                                            "body"=> $request->text,
                                        ]
                            ]),
                        ]);
            
            $data = json_decode($response->getBody());

            $whatsapp = new WhatsAppMessage();
            $whatsapp->message_id = $data->messages[0]->id;
            $whatsapp->recipient_phone = $request->recipient_phone_number;
            $whatsapp->type = "text";
            $whatsapp->text_body = $request->text;
            $whatsapp->message_sent = Carbon::now();
            $whatsapp->save();
            
            return ['success' => 1, "data"=>$whatsapp, "message" => "Se envió el mensaje exitosamente."];
            
        }catch(\Throwable $th){
            return ['success' => 0, "message" => "No se pudo enviar el documento.", "error"=> $th];
        }

		


    }

    public function sendDocumentMessage(Request $request){

        $upload_media = $this->uploadMediaWhatsAppCloud($request);
        if($upload_media['success'] == 1){
            // Una vez que se carga el archivo, ahora si se envia el whatsapp con el documento
            try{

                $data_upload_media = $upload_media['data'];

                $client = new Client([
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => "Bearer ".$this->user_access_token,
                        'Cookie' => 'ps_l=0; ps_n=0'
                    ],
                    'base_uri' => "https://graph.facebook.com/".$this->version,
                ]);

                $response = $client->request('POST', "/".$this->phone_number_id_dmi."/messages", [
                    'body' => json_encode([
                        "messaging_product" => "whatsapp",
                        "recipient_type" => "individual",
                        "to" => "$request->recipient_phone_number",
                        "type" => "document",
                        "document" => [
                            "id" => $data_upload_media['document_object_id'],
                            "caption" => $request->text,
                            "filename" => $data_upload_media['document_filename']
                        ]
                    ]),
                ]);
    
                $data = json_decode($response->getBody()); 

                $whatsapp = new WhatsAppMessage();
                $whatsapp->message_id = $data->messages[0]->id;
                $whatsapp->recipient_phone = $request->recipient_phone_number;
                $whatsapp->type = "document";
                $whatsapp->text_body = $request->text;
                $whatsapp->document_url = "Url de la ubicación del archivo";
                $whatsapp->document_filename = $data_upload_media['document_filename'];
                $whatsapp->document_id = $data_upload_media['document_object_id'];
                $whatsapp->document_content_type = $data_upload_media['document_content_type'];
                $whatsapp->message_sent = Carbon::now();
                $whatsapp->save();
                
                return ['success' => 1, "data"=>$whatsapp, "message" => "Se envió el documento exitosamente."];

            }catch(\Throwable $th){
                return ['success' => 0, "message" => "No se pudo enviar el documento.", "error"=> $th];
            }

        }else{
            return ['success' => 0, "message" => "No se pudo hacer la carga del documento."];
        }

    }

    public function uploadMediaWhatsAppCloud($_request){

        $file = $_request->file('file');
    
        try {
            $client = new Client();
            $headers = [
                            'Authorization' => "Bearer ".$this->user_access_token,
                            'Cookie' => 'ps_l=0; ps_n=0'
            ];

            $options = [
                'multipart' => [
                    [
                        'name' => 'messaging_product',
                        'contents' => 'whatsapp'
                    ],
                    [
                        'name' => 'file',
                        'contents' => fopen($file->getPathname(), 'r'),
                        'filename' => $file->getClientOriginalName(),
                        'headers'  => [
                            'Content-Type' => $file->getMimeType(),
                        ]
                    ]
                ]
            ];

            $request = new GuzzleHttp_Request('POST', "https://graph.facebook.com/".$this->version."/".$this->phone_number_id_dmi."/media", $headers);
            $res = $client->sendAsync($request, $options)->wait();
            $response = json_decode($res->getBody());
            
            return [
                'success' => 1, 
                "data" =>[
                            "document_object_id" => $response->id,
                            "document_filename" => $file->getClientOriginalName(),
                            "document_content_type" => $file->getMimeType(),
                        ], 
                "message" => ""
            ];

        } catch (\Throwable $th) {
            return ['success' => 0, "message" => "No se pudo hacer la carga del archivo","error" => $th];
        }
        
    }

    public function sendDocumentByURL(Request $request){

        $client = new Client([
			'headers' => [
				'Content-Type' => 'application/json',
				'Authorization' => "Bearer ".$this->user_access_token,
				'Cookie' => 'ps_l=0; ps_n=0'
			],
			'base_uri' => "https://graph.facebook.com/".$this->version,
		]);

        $response = $client->request('POST', "/".$this->phone_number_id_dmi."/messages", [
            'body' => json_encode([
                "messaging_product"=> "whatsapp",
                "recipient_type"=> "individual",
                "to"=> "$request->recipient_phone_number",
                "type"=> "document",
                "document"=> [
                            "link"=> $request->url,
                            //"link"=> "http://localhost:8001/storage/IncidenciasIntranet/2024/Vacaciones/N2006/200601/N0060718_ELADIO_PEREZ_ROBLEDO_2529_Q06.pdf",
                            "caption"=> "Ingresa a este link para visualizar el documento.",
                        ]
            ]),
        ]);

        $data = json_decode($response->getBody());

        return $data;
    }

    public function sendImageMessage(Request $request){

        $upload_media = $this->uploadMediaWhatsAppCloud($request);
        if($upload_media['success'] == 1){
            // Una vez que se carga el archivo, ahora si se envia el whatsapp con el documento
            try{

                $data_upload_media = $upload_media['data'];

                $client = new Client([
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => "Bearer ".$this->user_access_token,
                        'Cookie' => 'ps_l=0; ps_n=0'
                    ],
                    'base_uri' => "https://graph.facebook.com/".$this->version,
                ]);

                $response = $client->request('POST', "/".$this->phone_number_id_dmi."/messages", [
                    'body' => json_encode([
                        "messaging_product" => "whatsapp",
                        "recipient_type" => "individual",
                        "to" => "$request->recipient_phone_number",
                        "type" => "image",
                        "image" => [
                            "id" => $data_upload_media['document_object_id'],
                        ]
                    ]),
                ]);
    
                $data = json_decode($response->getBody());
                
                $whatsapp = new WhatsAppMessage();
                $whatsapp->message_id = $data->messages[0]->id;
                $whatsapp->recipient_phone = $request->recipient_phone_number;
                $whatsapp->type = "image";
                $whatsapp->document_url = "Url de la ubicación de la imagen";
                $whatsapp->document_filename = $data_upload_media['document_filename'];
                $whatsapp->document_id = $data_upload_media['document_object_id'];
                $whatsapp->document_content_type = $data_upload_media['document_content_type'];
                $whatsapp->message_sent = Carbon::now();
                $whatsapp->save();

                return ['success' => 1, "data"=>$whatsapp, "message" => "Se envió la imagen exitosamente."];

            }catch(\Throwable $th){
                return ['success' => 0, "message" => "No se pudo enviar la imagen.", "error"=> $th];
            }

        }else{
            return ['success' => 0, "message" => "No se pudo hacer la carga de la imagen."];
        }

    }

    public function uploadImageMediaWhatsAppCloud($_request){
        
        $file = $_request->file('file');

        try {
            $client = new Client();
            $headers = [
                            'Authorization' => "Bearer ".$this->user_access_token,
                            'Cookie' => 'ps_l=0; ps_n=0'
            ];

            $options = [
                'multipart' => [
                    [
                        'name' => 'messaging_product',
                        'contents' => 'whatsapp'
                    ],
                    [
                        'name' => 'file',
                        'contents' => file_get_contents($file->getRealPath()),
                        'filename' => $file->getClientOriginalName(),
                        'headers'  => [
                            'Content-Type' => $file->getMimeType(),
                        ]
                    ]
                ]
            ];

            $request = new GuzzleHttp_Request('POST', "https://graph.facebook.com/".$this->version."/".$this->phone_number_id_dmi."/media", $headers);
            $res = $client->sendAsync($request, $options)->wait();
            $response = json_decode($res->getBody());
            
            return [
                'success' => 1, 
                "data" =>[
                            "document_object_id" => $response->id,
                            "document_filename" => $file->getClientOriginalName(),
                            "document_content_type" => $file->getMimeType(),
                        ], 
                "message" => ""
            ];

        } catch (\Throwable $th) {
            return ['success' => 0, "message" => "No se pudo hacer la carga del archivo","error" => $th];
        }
        
    }

    public function sendTemplateMessage(Request $request){

        try {
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer ".$this->user_access_token,
                    'Cookie' => 'ps_l=0; ps_n=0'
                ],
                'base_uri' => "https://graph.facebook.com/".$this->version,
            ]);
    
            
            $response = $client->request('POST', "/".$this->phone_number_id_dmi."/messages", [
                            'body' => json_encode([
                                "messaging_product"=> "whatsapp",
                                "recipient_type"=> "individual",
                                "to"=> $request->recipient_phone_number,
                                "type"=> "template",
                                "template"=> [
                                            "name"=> "dmi_saludo_inicial_colaborador",
                                            "language"=> ['code' => "es_MX"],
                                        ]
                            ]),
                        ]);
            
            $data = json_decode($response->getBody());
    
            $whatsapp = new WhatsAppMessage();
            $whatsapp->message_id = $data->messages[0]->id;
            $whatsapp->recipient_phone = $request->recipient_phone_number;
            $whatsapp->type = "template";
            $whatsapp->template_name = "dmi_saludo_inicial_colaborador";
            $whatsapp->template_language_code = "es_MX";
            $whatsapp->message_sent = Carbon::now();
            $whatsapp->save();
            
            return ['success' => 1, "data"=>$whatsapp, "message" => "Se envió la imagen exitosamente."];

        } catch (\Throwable $th) {
            return ['success' => 0, "message" => "No se pudo hacer el envio del mensaje","error" => $th];
        }

    }

    /* public function sendTemplateMessage(Request $request){

        try {
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer ".$this->user_access_token,
                    'Cookie' => 'ps_l=0; ps_n=0'
                ],
                'base_uri' => "https://graph.facebook.com/".$this->version,
            ]);
    
            
            $response = $client->request('POST', "/".$this->phone_number_id_dmi."/messages", [
                            'body' => json_encode([
                                "messaging_product"=> "whatsapp",
                                "recipient_type"=> "individual",
                                "to"=> $request->recipient_phone_number,
                                "type"=> "template",
                                "template"=> [
                                    "name"=> "dmi_saludo_inicial_recibo",
                                    "language"=> [
                                        "code"=> "es_MX"
                                    ],
                                    "components"=> [
                                        [
                                            "type"=> "body",
                                            "parameters"=> [
                                                [
                                                    "type"=> "text",
                                                    "text"=> "Eladio"
                                                    ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]),
                        ]);
            
            $data = json_decode($response->getBody());
    
            $whatsapp = new WhatsAppMessage();
            $whatsapp->message_id = $data->messages[0]->id;
            $whatsapp->recipient_phone = $request->recipient_phone_number;
            $whatsapp->type = "template";
            $whatsapp->template_name = "dmi_saludo_inicial_recibo";
            $whatsapp->template_language_code = "es_MX";
            $whatsapp->message_sent = Carbon::now();
            $whatsapp->save();
            
            return ['success' => 1, "data"=>$whatsapp, "message" => "Se envió la imagen exitosamente."];

        } catch (Exception $th) {
            return ['success' => 0, "message" => "No se pudo hacer el envio del mensaje","error" => $th->getMessage()];
        }

    } */

    public function getPayrollReceipt(){
        
        /* $list = $this->ToolsController->getPayroll(2024)->getData(false)[0];

        //sreturn $list;

        $temp_request = new Request();
        $temp_request->setMethod('POST');
        $temp_request->query->add(['payroll'=>['company_code' => $list->Empresa,
                                                'year' => $list->Ejercicio,
                                                'start_month' => $list->Periodo,
                                                'payroll_code' => $list->MovID,
                                                'rfc' => $list->RFC,
                                                ],
                                    'type' => 1
                                ]);
        $document_pdf = $this->ToolsController->getFilePayroll($temp_request);

        $client = new Client();
        $headers = [
                        'Authorization' => "Bearer ".$this->user_access_token,
                        'Cookie' => 'ps_l=0; ps_n=0'
        ];

        $options = [
            'multipart' => [
                [
                    'name' => 'messaging_product',
                    'contents' => 'whatsapp'
                ],
                [
                    'name' => 'file',
                    'contents' => base64_decode($document_pdf),
                    'filename' => "Documento Eladio",
                    'headers'  => [
                        'Content-Type' => "application/pdf",
                    ]
                ]
            ]
        ];

        $request = new GuzzleHttp_Request('POST', "https://graph.facebook.com/".$this->version."/".$this->phone_number_id_dmi."/media", $headers);
        $res = $client->sendAsync($request, $options)->wait();
        $response = json_decode($res->getBody());

        return $response; */

        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer ".$this->user_access_token,
                'Cookie' => 'ps_l=0; ps_n=0'
            ],
            'base_uri' => "https://graph.facebook.com/".$this->version,
        ]);

        $response = $client->request('POST', "/".$this->phone_number_id_dmi."/messages", [
            'body' => json_encode([
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => "524371037356",
                "type" => "document",
                "document" => [
                    "id" => "1461453777791627",
                    "caption" => "Documento Eladio",
                    "filename" => "documento_eladio.pdf"
                ]
            ]),
        ]);

        $data = json_decode($response->getBody());

    }

    public function test(){
        return "test whatsapp";
    }

    public function verifyWebhook(Request $request){
        
        try {

            $token = $this->token_webhook_meta;
            $query = $request->query();
            $_mode = isset($query['hub_mode']) != null ? $query['hub_mode'] : null;
            $_verify_token = isset($query['hub_verify_token']) != null ? $query['hub_verify_token'] : null;
            $_challenge = isset($query['hub_challenge']) != null ? $query['hub_challenge'] : null;

            if($_mode != null && $_verify_token != null){
                if($_mode === "subscribe" && $_verify_token === $token){
                    
                    return response($_challenge,200)->header("Content-Type","text/plain");
                }
            }

            throw new Exception("Invalid token",500);
            
            
        } catch (Exception $th) {
            return response()->json([
                "success" => 0,
                "error" => $th->getMessage(),
            ],500);
        }

    }

    public function processWebhook(Request $request){
        try {
            $_params = json_decode($request->getContent(),true);
            $body = "";

            

            if(isset($_params['entry'])){
                $data_message = $_params['entry'][0]['changes'][0]['value'];

                // Se valia que tipo de acción es Mensaje | Estatus
                if(isset($data_message['messages'])){
                    // Guardamos el mensaje que nos mandan
                    $whatsapp = new WhatsAppMessage();
                    $whatsapp->message_id = $data_message['messages'][0]['id'];
                    $whatsapp->recipient_phone = $data_message['messages'][0]['from'];
                    $whatsapp->type = $data_message['messages'][0]['type'];
                    $whatsapp->text_body = $data_message['messages'][0]['text']['body'];
                    $whatsapp->owner = $data_message['contacts'][0]['profile']['name'];
                    $whatsapp->message_sent = Carbon::createFromTimestamp($data_message['messages'][0]['timestamp'])->toDateTimeString();
                    $whatsapp->save();

                }else if(isset($data_message['statuses'])){
                    // Buscamos el mensaje, para agregar el status del mensaje

                    $status = WhatsAppMessage::where('message_id',$data_message['statuses'][0]['id'])->first();

                    if($status != null){
                        if($data_message['statuses'][0]['status'] == "sent"){
                            $status->message_sent = Carbon::createFromTimestamp($data_message['statuses'][0]['timestamp'])->toDateTimeString();
                            $status->save();
                            $body = "Status - Ok";
                            
                        }elseif($data_message['statuses'][0]['status'] == "delivered"){
                            $status->message_delivered = Carbon::createFromTimestamp($data_message['statuses'][0]['timestamp'])->toDateTimeString();
                            $status->save();
                            $body = "Status - Ok";

                        }elseif($data_message['statuses'][0]['status'] == "read"){
                            $status->message_read = Carbon::createFromTimestamp($data_message['statuses'][0]['timestamp'])->toDateTimeString();
                            $status->save();
                            $body = "Status - Ok";

                        }else{
                            $body = "Error";
                        }

                    }else{
                        $body = "Status - Not found";
                    }

                }               
            }

            return response()->json([
                "success" => 1,
                "message" => "ok",
                "data" => $body
            ],200);
            
            
        } catch (Exception $th) {
            return response()->json([
                "success" => 0,
                "error" => $th->getMessage(),
            ],500);
        }
    }

    /* Start - Servicio para el envio de la nómina */
    public function sendPayrollWhatsAppCloud($_payroll){
        $upload_media = $this->uploadPayrollWhatsAppCloud($_payroll);
        $to_phone_number = "52".$_payroll['phone_number'];

        if($upload_media['success'] == 1){
            // Una vez que se carga el archivo, ahora si se envia el whatsapp con el documento
            try{

                $data_upload_media = $upload_media['data'];

                $client = new Client([
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => "Bearer ".$this->user_access_token,
                        'Cookie' => 'ps_l=0; ps_n=0'
                    ],
                    'base_uri' => "https://graph.facebook.com/".$this->version,
                ]);

                $response = $client->request('POST', "/".$this->phone_number_id_dmi."/messages", [
                    'body' => json_encode([
                        "messaging_product" => "whatsapp",
                        "recipient_type" => "individual",
                        "to" => $to_phone_number,
                        "type" => "document",
                        "document" => [
                            "id" => $data_upload_media['document_object_id'],
                            "caption" => $_payroll['title'],
                            "filename" => $data_upload_media['document_filename']
                        ]
                    ]),
                ]);
    
                $data = json_decode($response->getBody()); 

                $whatsapp = new WhatsAppMessage();
                $whatsapp->message_id = $data->messages[0]->id;
                $whatsapp->recipient_phone = $to_phone_number;
                $whatsapp->type = "document";
                $whatsapp->text_body = $_payroll['title'];
                $whatsapp->document_url = "Url de la ubicación del archivo";
                $whatsapp->document_filename = $data_upload_media['document_filename'];
                $whatsapp->document_id = $data_upload_media['document_object_id'];
                $whatsapp->document_content_type = $data_upload_media['document_content_type'];
                $whatsapp->message_sent = Carbon::now();
                $whatsapp->save();
                
                return ['success' => 1, "data"=>$whatsapp, "message" => "Se envió el documento exitosamente."];

            }catch(\Exception $th){
                return ['success' => 0, "message" => "No se pudo enviar el documento.", "error"=> $th->getMessage()];
            }

        }else{
            return ['success' => 0, "message" => "No se pudo hacer la carga del documento."];
        }
    }

    public function sendTemplatePayrollWhatsAppCloud($_payroll){
        $upload_media = $this->uploadPayrollWhatsAppCloud($_payroll);
        $to_phone_number = "52".$_payroll['phone_number'];

        if($upload_media['success'] == 1){
            // Una vez que se carga el archivo, ahora si se envia el whatsapp con el documento
            try{

                $data_upload_media = $upload_media['data'];

                $client = new Client([
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => "Bearer ".$this->user_access_token,
                        'Cookie' => 'ps_l=0; ps_n=0'
                    ],
                    'base_uri' => "https://graph.facebook.com/".$this->version,
                ]);

                $response = $client->request('POST', "/".$this->phone_number_id_dmi."/messages", [
                    'body' => json_encode([
                        "messaging_product" => "whatsapp",
                        "recipient_type" => "individual",
                        "to" => $to_phone_number,
                        "type" => "template",
                        "template" => [
                            "name"=> "dmi_envio_recibo_nomina",
                            "language" => [
                              "policy" => "deterministic",
                              "code" => "es_MX"
                            ],
                            "components" => [
                                [
                                "type" => "header",
                                "parameters" => [
                                    [
                                        "type" => "document",
                                        "document" => [
                                            "id" => $data_upload_media['document_object_id'],
                                            "filename" => $data_upload_media['document_filename']
                                        ]
                                    ]
                                ]
                                ],
                                [
                                    "type" => "body",
                                    "parameters" => [
                                        [
                                            "type" => "text",
                                            "text" => $_payroll['title']
                                        ]
                                    ]
                                ]
                            ]
                        ]
                        /* "document" => [
                            "id" => $data_upload_media['document_object_id'],
                            "caption" => $_payroll['title'],
                            "filename" => $data_upload_media['document_filename']
                        ] */
                    ]),
                ]);
    
                $data = json_decode($response->getBody()); 

                $whatsapp = new WhatsAppMessage();
                $whatsapp->message_id = $data->messages[0]->id;
                $whatsapp->recipient_phone = $to_phone_number;
                $whatsapp->type = "document";
                $whatsapp->text_body = $_payroll['title'];
                $whatsapp->document_url = "Url de la ubicación del archivo";
                $whatsapp->document_filename = $data_upload_media['document_filename'];
                $whatsapp->document_id = $data_upload_media['document_object_id'];
                $whatsapp->document_content_type = $data_upload_media['document_content_type'];
                $whatsapp->message_sent = Carbon::now();
                $whatsapp->save();
                
                return ['success' => 1, "data"=>$whatsapp, "message" => "Se envió el documento exitosamente."];

            }catch(\Exception $th){
                return ['success' => 0, "message" => "No se pudo enviar el documento.", "error"=> $th->getMessage()];
            }

        }else{
            return ['success' => 0, "message" => "No se pudo hacer la carga del documento."];
        }
    }

    public function uploadPayrollWhatsAppCloud($_payroll){
    
        try {
            $client = new Client();
            $headers = [
                            'Authorization' => "Bearer ".$this->user_access_token,
                            'Cookie' => 'ps_l=0; ps_n=0'
            ];

            $options = [
                'multipart' => [
                    [
                        'name' => 'messaging_product',
                        'contents' => 'whatsapp'
                    ],
                    [
                        'name' => 'file',
                        'contents' => $_payroll['document'],
                        'filename' => $_payroll['document_name'],
                        'headers'  => [
                            'Content-Type' => $_payroll['document_content_type'],
                        ]
                    ]
                ]
            ];

            $request = new GuzzleHttp_Request('POST', "https://graph.facebook.com/".$this->version."/".$this->phone_number_id_dmi."/media", $headers);
            $res = $client->sendAsync($request, $options)->wait();
            $response = json_decode($res->getBody());
            
            return [
                'success' => 1, 
                "data" =>[
                            "document_object_id" => $response->id,
                            "document_filename" => $_payroll['document_name'],
                            "document_content_type" => $_payroll['document_content_type'],
                        ], 
                "message" => "Se cargo con éxito el documento"
            ];

        } catch (\Exception $th) {
            return ['success' => 0, "message" => "No se pudo hacer la carga del archivo","error" => $th->getMessage()];
        }
    }

    public function sendWhatsAppStartConversation($_data){

        $to_phone_number = "52".$_data['recipient_phone_number'];

        try {
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer ".$this->user_access_token,
                    'Cookie' => 'ps_l=0; ps_n=0'
                ],
                'base_uri' => "https://graph.facebook.com/".$this->version,
            ]);
    
            
            $response = $client->request('POST', "/".$this->phone_number_id_dmi."/messages", [
                            'body' => json_encode([
                                "messaging_product"=> "whatsapp",
                                "recipient_type"=> "individual",
                                "to"=> $to_phone_number,
                                "type"=> "template",
                                "template"=> [
                                    "name"=> "dmi_saludo_inicial_recibo",
                                    "language"=> [
                                        "code"=> "es_MX"
                                    ],
                                    "components"=> [
                                        [
                                            "type"=> "body",
                                            "parameters"=> [
                                                [
                                                    "type"=> "text",
                                                    "text"=> $_data['collaborador_name'],
                                                    ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]),
                        ]);
            
            $data = json_decode($response->getBody());
    
            $whatsapp = new WhatsAppMessage();
            $whatsapp->message_id = $data->messages[0]->id;
            $whatsapp->recipient_phone = $to_phone_number;
            $whatsapp->type = "template";
            $whatsapp->template_name = "dmi_saludo_inicial_recibo";
            $whatsapp->template_language_code = "es_MX";
            $whatsapp->message_sent = Carbon::now();
            $whatsapp->save();
            
            return ['success' => 1, "data"=>$whatsapp, "message" => "Se envió el mensaje template exitosamente."];

        } catch (Exception $th) {
            return ['success' => 0, "message" => "No se pudo hacer el envio del mensaje","error" => $th->getMessage()];
        }

    }
    /* End - Servicio para el envio de la nómina */


}
