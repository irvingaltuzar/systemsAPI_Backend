<?php

namespace App\Http\Controllers\Dmihd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dmihd\DmihdPrioritySubArea;
use App\Models\Dmihd\DmihdPriority;
use App\Models\Dmihd\DmihdSubArea;
use Illuminate\Support\Facades\DB;

class PriorityController extends Controller
{
    public function index(){
        $res=DmihdPrioritySubArea::with(
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

        )->groupBy("dmihd_sub_area_id")->get();
        return response()->json($res, 200);
    }
    public function store(Request $request)
    {

        try {

            $duplicada=DmihdPrioritySubArea::where('dmihd_sub_area_id',$request->sub_area)->count();

            if($duplicada>0){
                return response()->json(['error' => 'Ya existe esta sub-área con prioridades'], 201);
            }
            $baja= DmihdPriority::where('priority','Baja')->first();

            $priority_baja=new DmihdPrioritySubArea();
            $priority_baja->dmihd_sub_area_id= $request->sub_area;
            $priority_baja->dmihd_priority_id= $baja->id;
            $priority_baja->solve_format= $request->baja_select_sol;
            $priority_baja->time_solve= $request->baja_sol;
            $priority_baja->response_format= $request->baja_select_res;
            $priority_baja->response_time= $request->baja_num;

            $media= DmihdPriority::where('priority','Media')->first();
            $priority_media=new DmihdPrioritySubArea();
            $priority_media->dmihd_sub_area_id= $request->sub_area;
            $priority_media->dmihd_priority_id= $media->id;
            $priority_media->solve_format= $request->media_select_sol;
            $priority_media->time_solve= $request->media_sol;
            $priority_media->response_format= $request->media_select_res;
            $priority_media->response_time= $request->media_num;

            $alta= DmihdPriority::where('priority','Alta')->first();
            $priority_alta=new DmihdPrioritySubArea();
            $priority_alta->dmihd_sub_area_id= $request->sub_area;
            $priority_alta->dmihd_priority_id= $alta->id;
            $priority_alta->solve_format= $request->alta_select_sol;
            $priority_alta->time_solve= $request->alta_sol;
            $priority_alta->response_format= $request->alta_select_res;
            $priority_alta->response_time= $request->alta_num;

            $urgente= DmihdPriority::where('priority','Urgente')->first();
            $priority_urgente=new DmihdPrioritySubArea();
            $priority_urgente->dmihd_sub_area_id= $request->sub_area;
            $priority_urgente->dmihd_priority_id= $urgente->id;
            $priority_urgente->solve_format= $request->urgente_select_sol;
            $priority_urgente->time_solve= $request->urgente_sol;
            $priority_urgente->response_format= $request->urgente_select_res;
            $priority_urgente->response_time= $request->urgente_num;

            if($priority_baja->save() && $priority_media->save() && $priority_alta->save() && $priority_urgente->save()){
                return response()->json(['success'=>'Se guardo exitosamente.'],200);
            }
            return response()->json(['error' => 'Error al guardar'], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th], 500);
        }
    }
    public function show($id){
        $baja=DB::table('dmihd_priority_sub_areas')
        ->select('dmihd_priority_sub_areas.*')
        ->join('dmihd_priorities', 'dmihd_priorities.id', '=',
         'dmihd_priority_sub_areas.dmihd_priority_id')
        ->where('dmihd_sub_area_id',$id)
        ->where('dmihd_priorities.priority','Baja')->first();

        $media=DB::table('dmihd_priority_sub_areas')
        ->select('dmihd_priority_sub_areas.*')
        ->join('dmihd_priorities', 'dmihd_priorities.id', '=',
         'dmihd_priority_sub_areas.dmihd_priority_id')
        ->where('dmihd_sub_area_id',$id)
        ->where('dmihd_priorities.priority','Media')->first();

        $alta=DB::table('dmihd_priority_sub_areas')
        ->select('dmihd_priority_sub_areas.*')
        ->join('dmihd_priorities', 'dmihd_priorities.id', '=',
         'dmihd_priority_sub_areas.dmihd_priority_id')
        ->where('dmihd_sub_area_id',$id)
        ->where('dmihd_priorities.priority','Alta')->first();

        $urgente=DB::table('dmihd_priority_sub_areas')
        ->select('dmihd_priority_sub_areas.*')
        ->join('dmihd_priorities', 'dmihd_priorities.id', '=',
         'dmihd_priority_sub_areas.dmihd_priority_id')
        ->where('dmihd_sub_area_id',$id)
        ->where('dmihd_priorities.priority','Urgente')->first();

        $sub_area=DmihdSubArea::with(
            [
                'area'=> function($query){
                    $query->withTrashed();
                },
                'area.location'=> function($query){
                    $query->withTrashed();
                }
            ]
        )->where('id',$id)->withTrashed()->first();

        return ['sub_area'=>$sub_area,'baja'=>$baja,'media'=>$media,'alta'=>$alta,'urgente'=>$urgente];
    }

    public function edit(Request $request)
    {

        try {

            $duplicada=DmihdPrioritySubArea::where('dmihd_sub_area_id',$request->sub_area)->
            where('id','!=', $request->baja_id)->
            where('id','!=', $request->media_id)->
            where('id','!=', $request->alta_id)->
            where('id','!=', $request->urgente_id)->count();
            if($duplicada>0){
                return response()->json(['error' => 'Ya existe esta sub-área con prioridades'], 201);
            }
            $priority_baja=DmihdPrioritySubArea::where('id', $request->baja_id)->first();
            $priority_baja->dmihd_sub_area_id= $request->sub_area;
            $priority_baja->solve_format= $request->baja_select_sol;
            $priority_baja->time_solve= $request->baja_sol;
            $priority_baja->response_format= $request->baja_select_res;
            $priority_baja->response_time= $request->baja_num;

            $priority_media=DmihdPrioritySubArea::where('id', $request->media_id)->first();
            $priority_media->dmihd_sub_area_id= $request->sub_area;
            $priority_media->solve_format= $request->media_select_sol;
            $priority_media->time_solve= $request->media_sol;
            $priority_media->response_format= $request->media_select_res;
            $priority_media->response_time= $request->media_num;

            $priority_alta=DmihdPrioritySubArea::where('id', $request->alta_id)->first();
            $priority_alta->dmihd_sub_area_id= $request->sub_area;
            $priority_alta->solve_format= $request->alta_select_sol;
            $priority_alta->time_solve= $request->alta_sol;
            $priority_alta->response_format= $request->alta_select_res;
            $priority_alta->response_time= $request->alta_num;

            $priority_urgente=DmihdPrioritySubArea::where('id', $request->urgente_id)->first();
            $priority_urgente->dmihd_sub_area_id= $request->sub_area;
            $priority_urgente->solve_format= $request->urgente_select_sol;
            $priority_urgente->time_solve= $request->urgente_sol;
            $priority_urgente->response_format= $request->urgente_select_res;
            $priority_urgente->response_time= $request->urgente_num;

            if($priority_baja->update() && $priority_media->update() && $priority_alta->update() && $priority_urgente->update()){
                return response()->json(['success'=>'Se actualizo exitosamente.'],200);
            }
            return response()->json(['error' => 'Error al guardar'], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th], 500);
        }
    }

    public function destroy($id)
    {
        try {
            if(DmihdPrioritySubArea::where('dmihd_sub_area_id',$id)->delete()) {
                return response()->json(['success'=>'Se eliminmo exitosamente.'],200);
            }
            return response()->json(['error' => 'Error al guardar'], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th], 500);
        }
    }
}
