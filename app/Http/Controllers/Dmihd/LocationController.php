<?php

namespace App\Http\Controllers\Dmihd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dmihd\DmihdLocation;

class LocationController extends Controller
{
    public function index(){
        return DmihdLocation::all();
    }

    public function store(Request $request){
        try {
            $rules = [
                'location' =>'required|unique:dmihd_locations',
            ];
    
            $messages = [
                'location.required' => 'El campo ubicación es requerido',
                'location.unique' => 'Ya existe está ubicación'
            ];
    
            $this->validate($request, $rules, $messages);
            $location= new DmihdLocation();
            $location->location=$request->location;
            if ($location->save()) {
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
            $rules = [
                'location' =>'required|unique:dmihd_locations,location,'.$request->id
            ];
    
            $messages = [
                'location.required' => 'El campo ubicación es requerido',
                'location.unique' => 'Ya existe está ubicación'
            ];
    
            $this->validate($request, $rules, $messages);
            $location= DmihdLocation::findOrFail($request->id);
            $location->location=$request->location;
            if ($location->update()) {
                return response()->json(['success'=>'Se actualizo exitosamente.'],200);
            }
            return response()->json(['error' => 'Error al actualizar'], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th], 500);
        }
        
    }

    public function destroy($id)
    {
        try {
            $location= DmihdLocation::findOrFail($id);
            if ($location->delete()) {
                return response()->json(['success'=>'Se eliminmo exitosamente.'],200);
            }
            return response()->json(['error' => 'Error al elminar'], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th], 500);
        }
    }
    
}
