<?php

namespace App\Http\Controllers\Dmihd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dmihd\DmihdUserTicket;
use App\Models\Intranet\PersonalIntelisis;

class UserTicketController extends Controller
{
    
    public function index(){
        return DmihdUserTicket::with(
            [
                'subArea'=> function($query){
                    $query->withTrashed();
                },
                'subArea.area'=> function($query){
                    $query->withTrashed();
                }, 
                'subArea.area.location'=> function($query){
                    $query->withTrashed();
                }
            ]
        )->join('intranet.personal_intelisis as db2','dmihd_user_tickets.user_id','=','db2.personal_intelisis_id')
        ->select('dmihd_user_tickets.*','db2.nombre','db2.apellidos','db2.personal_intelisis_id')->get();
    }
    public function store(Request $request){
        try {
            $rules = [
                'area' =>'required',
                'location' =>'required',
                'sub_area' => 'required',
                'user' => 'required'
            ];
    
            $messages = [
                'area.required' => 'El campo área es requerido',
                'location.required' => 'El campo ubicación es requerido',
                'area.required' => 'El campo sub-área es requerido',
                'user.required' => 'El campo usuario es requerido',
            ];
    
            $this->validate($request, $rules, $messages);
            $duplicado=DmihdUserTicket::where('user_id',$request->user)->where('dmihd_sub_area_id',$request->sub_area)->count();
            if($duplicado>0){
                return response()->json(['error' => 'Ya existe  este usuario en esta sub-área'], 201);
            }
            $user_ticket=new DmihdUserTicket();
            $user_ticket->user_id=$request->user;
            $user_ticket->leader=($request->leader==true)? 'si':'no';
            $user_ticket->receive_requests=($request->receive_requests==true)? 'si':'no';
            $user_ticket->dmihd_sub_area_id=$request->sub_area;
            if($user_ticket->save()){
                return response()->json(['success'=>'Se guardo exitosamente.'],200);
            }
            return response()->json(['error' => 'Error al guardar'], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th], 500);  
        }
    }
    public function edit(Request $request)
    {
        try {
            $duplicado=DmihdUserTicket::where('id','!=',$request->id)->where('user_id',$request->user)->where('dmihd_sub_area_id',$request->sub_area)->count();
            if($duplicado>0){
                return response()->json(['error' => 'Ya existe este usuario en esta sub-área'], 201);
            }
            $user_ticket=DmihdUserTicket::findOrFail($request->id);
            $user_ticket->user_id=$request->user;
            $user_ticket->leader=($request->leader==true)? 'si':'no';
            $user_ticket->receive_requests=($request->receive_requests==true)? 'si':'no';
            $user_ticket->dmihd_sub_area_id=$request->sub_area;
            if($user_ticket->update()){
                return response()->json(['success'=>'Se actualizado exitosamente.'],200);
            }
            return response()->json(['error' => 'Error al actualizar'], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th], 500); 
        }
        
    }
    public function destroy($id)
    {
        try {
            $user=DmihdUserTicket::findOrFail($id);
            if($user->delete()) {
                return response()->json(['success'=>'Se eliminmo exitosamente.'],200);
            }
            return response()->json(['error' => 'Error al guardar'], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th], 500);
        }
    }
    public function getUserView(){
        return PersonalIntelisis::select('personal_intelisis_id', 'nombre', 'apellidos')->where('estatus','ALTA')->get();
    }
    public function getUserSubArea($id){
       return DmihdUserTicket::where('dmihd_sub_area_id',$id)
       ->join('intranet.personal_intelisis as db2','dmihd_user_tickets.user_id','=','db2.personal_intelisis_id')
        ->select('dmihd_user_tickets.*','db2.nombre','db2.apellidos','db2.personal_intelisis_id')
        ->where('receive_requests','si')->get();
    }
}
