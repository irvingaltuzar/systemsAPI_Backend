<?php

namespace App\Http\Controllers\Dmihd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dmihd\DmihdArea;

class AreaController extends Controller
{
    public function index(){
        return DmihdArea::with(['location'=> function($query){
            $query->withTrashed();
        }])->get();
    }
    public function store(Request $request){
        try {
            $rules = [
                'area' =>'required',
                'location' =>'required'
            ];
    
            $messages = [
                'area.required' => 'El campo área es requerido',
                'location.required' => 'El campo ubicación es requerido',
            ];
    
            $this->validate($request, $rules, $messages);
            $duplicada=DmihdArea::where('dmihd_location_id',$request->location)->where('area',$request->area)->count();
            if($duplicada>0){
                return response()->json(['error' => 'Ya existe el área en la ubicación'], 201);
            }
            $area=new DmihdArea();
            $area->dmihd_location_id= $request->location;
            $area->area=$request->area;
            if($area->save()){
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
            $duplicada=DmihdArea::where('id','!=',$request->id)->where('dmihd_location_id',$request->location)->where('area',$request->area)->count();
            if($duplicada>0){
                return response()->json(['error' => 'Ya existe el área en la ubicación'], 201);
            }
            $area=DmihdArea::findOrFail($request->id);
            $area->dmihd_location_id= $request->location;
            $area->area=$request->area;
            if($area->update()){
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
            $area=DmihdArea::findOrFail($id);
            if($area->delete()) {
                return response()->json(['success'=>'Se eliminmo exitosamente.'],200);
            }
            return response()->json(['error' => 'Error al guardar'], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th], 500);
        }
    }
    public function getAreaLocation($id){
        return DmihdArea::where('dmihd_location_id',$id)->get();
    }
}
