<?php

namespace App\Http\Controllers\Dmicotiza;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dmicotiza\DmicotizaStagePrice;
use App\Models\Dmicotiza\DmicotizaDepartment;
use App\Models\Dmicotiza\DmicotizaStage;

class StagePriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $dept = DmicotizaDepartment::with(['classification.stage' => function ($query) {
                                            $query->where('dmicotiza_stages.status', '=', DmicotizaStage::ACTIVE);
                                        }])
                                        ->where('id', $id)
                                        ->first();
        $price=null;
        foreach ($dept->classification->stage as $value) {
            $price=DmicotizaStagePrice::where('stage',$value->stage)->where('dmicotiza_department_id',$id)->first();
            if($price){
                $price=$price->price;
            }
        }
        $stage_price=DmicotizaStagePrice::select('id','stage','price')->where('dmicotiza_department_id',$id)->get();
        return ['price'=>$price,'stage_price'=>$stage_price];
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'price' => 'required',
                'stage' => 'required',
            ];
    
            $messages = [
                'price.required' => 'El campo precio es requerido',
                'stage.required' => 'El campo etapa es requerido',
            ];
            $this->validate($request, $rules, $messages);
            if($request->id!=""){
                $stage=DmicotizaStagePrice::with('department.statu')->findOrFail($request->id);
                if($stage->department->statu->status=='Disponible'){
                    $stage->dmicotiza_department_id=$request->department_id;
                    $stage->stage=$request->stage;
                    $stage->price=$request->price;
                    if($stage->update()){
                        return response()->json(['success' => 'El precio al departamento se actualizo correctamente.'], 200);
                    }
                }else{
                    return response()->json(['error' => 'No se puede actualizar el precio en ese estatus'], 202);
                }
                
                
            }else{
                $getStage=DmicotizaStagePrice::where('dmicotiza_department_id',$request->department_id)
                ->where('stage',$request->stage)->count();
                if($getStage>0){
                    return response()->json(['error' => 'Ya existe un registro con esa etapa'], 202);
                }
                $stage=new DmicotizaStagePrice();
                $stage->dmicotiza_department_id=$request->department_id;
                $stage->stage=$request->stage;
                $stage->price=$request->price;
                if($stage->save()){
                    return response()->json(['success' => 'El precio al departamento se agregado correctamente.'], 200);
                }
            }
            
            return response()->json(['error' => 'Error al insertar'], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $stage=DmicotizaStagePrice::with('department.statu')->findOrFail($id);
            if ($stage->department->statu->status=='Disponible') {
                if($stage->delete()){
                    return response()->json(['success' => 'El precio al departamento se elimino correctamente.'], 200);
                }
            }else{
                return response()->json(['error' => 'No se puede eliminar el precio en ese estatus'], 202);
            }
            
            return response()->json(['error' => 'Error al eliminar'], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th], 500);
        }
    }
}
