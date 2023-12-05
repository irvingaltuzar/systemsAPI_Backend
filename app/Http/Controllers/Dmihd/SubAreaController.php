<?php

namespace App\Http\Controllers\Dmihd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dmihd\DmihdSubArea;

class SubAreaController extends Controller
{
    public function index(){
        return DmihdSubArea::with(
            [
                'area'=> function($query){
                    $query->withTrashed();
                },
                'area.location'=> function($query){
                    $query->withTrashed();
                }
            ]
        )->get();
    }
    public function store(Request $request){
        try {
            $rules = [
                'area' =>'required',
                'location' =>'required',
                'sub_area' => 'required'
            ];
    
            $messages = [
                'area.required' => 'El campo área es requerido',
                'location.required' => 'El campo ubicación es requerido',
                'area.required' => 'El campo sub-área es requerido',
            ];
    
            $this->validate($request, $rules, $messages);
            $duplicada=DmihdSubArea::where('dmihd_area_id',$request->area)->where('sub_area',$request->sub_area)->count();
            if($duplicada>0){
                return response()->json(['error' => 'Ya existe  esta sub-área en el área'], 201);
            }
            $sub_area=new DmihdSubArea();
            $sub_area->dmihd_area_id=$request->area;
            $sub_area->sub_area=$request->sub_area;
            if($sub_area->save()){
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
            $duplicada=DmihdSubArea::where('id','!=',$request->id)->where('dmihd_area_id',$request->area)->where('sub_area',$request->sub_area)->count();
            if($duplicada>0){
                return response()->json(['error' => 'Ya existe la sub-área en el área'], 201);
            }
            $area=DmihdSubArea::findOrFail($request->id);
            $area->dmihd_area_id= $request->area;
            $area->sub_area=$request->sub_area;
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
            $area=DmihdSubArea::findOrFail($id);
            if($area->delete()) {
                return response()->json(['success'=>'Se eliminmo exitosamente.'],200);
            }
            return response()->json(['error' => 'Error al guardar'], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th], 500);
        }
    }
    public function getSubAreaArea($id)
    {
        return DmihdSubArea::where('dmihd_area_id',$id)->get();
    }
}
