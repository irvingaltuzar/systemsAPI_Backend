<?php

namespace App\Http\Controllers\Dmicotiza;

use App\Http\Controllers\Controller;
use App\Models\Dmicotiza\DmicotizaClassification;
use App\Models\Dmicotiza\DmicotizaDepartment;
use App\Models\Dmicotiza\DmicotizaAmenity;
use App\Models\Dmicotiza\DmicotizaProject;
use App\Models\Dmicotiza\DmicotizaProjectView;
use App\Models\Dmicotiza\DmicotizaStage;
use App\Models\Dmicotiza\DmicotizaStagePrice;
use App\Models\Dmicotiza\DmicotizaTypeDepartment;
use App\Models\Dmicotiza\DmicotizaStatu;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index($project)
    {
        $initialPlant = 1;
        $data = [];
        $grupo = false;
        $get_project = DmicotizaProject::where('id', $project)->first();
        if ($get_project) {
            if ($get_project->low_level) {
                $initialPlant = 0;
            }
            $views = DmicotizaProjectView::with(['subdivisions' => function ($query) {
                $query->orderBy('position');
            }, 'subdivisions.subdivisionGroup'])->where('dmicotiza_project_id', $project)->orderBy('position')->get();
            if ($views) {
                foreach ($views as $view) {
                    $arraySubdivision = [];
                    $arraySubdivisionGroups = [];
                    $arrayDepartment = [];
                    $rowColumn = [];
                    $contGroup = 0;
                    foreach ($view->subdivisions as $subdivision) {
                        array_push($arraySubdivision, ['name' => $subdivision->name]);
                        if ($contGroup == 0) {

                            if ($subdivision->subdivisionGroup) {
                                $grupo = true;
                                $contGroup = ($subdivision->subdivisionGroup->column) - 1;
                                array_push($arraySubdivisionGroups, ['name' => $subdivision->subdivisionGroup->group, 'column' => $subdivision->subdivisionGroup->column]);
                            } else {
                                array_push($arraySubdivisionGroups, ['name' => '', 'column' => 0]);
                            }

                        } else {
                            $contGroup--;
                        }
                    }

                    for ($i = $get_project->level; $i >= $initialPlant; $i--) {
                        $infoDepartment = [];
                        $cont = 0;
                        foreach ($view->subdivisions as $subdivision) {
                            $pintar = true;
                            foreach ($rowColumn as $value) {
                                if ($value['id'] == $subdivision->id && $value['fila'] < $i) {
                                    $cont = $value['column'];
                                    $pintar = false;
                                    break;
                                }
                            }

                            if ($cont == 0 && $pintar == true) {
                                $amenidad = DmicotizaAmenity::where('level', $i)
                                    ->where('dmicotiza_subdivision_id', $subdivision->id)
                                    ->where('dmicotiza_project_view_id', $view->id)->first();
                                if ($amenidad) {
                                    $cont = $amenidad->column - 1;
                                    if ($amenidad->row > 1) {
                                        array_push($rowColumn, ['id' => $subdivision->id,
                                            'column' => $amenidad->column,
                                            'fila' => $i - $amenidad->row,
                                        ]);
                                    }
                                    array_push($infoDepartment, ['info' => true,
                                        'row' => $amenidad->row,
                                        'column' => $amenidad->column,
                                        'amenidad' => true,
                                        'data' => $amenidad->name,
                                        'background' => '#fff',
                                        'color' => '#000',
                                        'subdivision' => $subdivision->id,
                                        'level' => $i,
                                        'id' => $amenidad->id,
                                    ]);
                                } else {
                                    $dept = DmicotizaDepartment::with(['classification','statu',
                                        'classification.stage' => function ($query) {
                                            $query->where('dmicotiza_stages.status', '=', DmicotizaStage::ACTIVE);
                                        },
                                        'typeDepartment'])
                                        ->where('dmicotiza_subdivision_id', $subdivision->id)
                                        ->where('level', $i)->where('dmicotiza_project_id', $project)
                                        ->first();
                                    if ($dept) {
                                        $background = ($dept->statu->status == 'Disponible') ? $dept->classification->color : '#000';
                                        $color = ($dept->statu->status == 'Disponible') ? '#000' : '#fff';
                                        $price = null;
                                        if ($dept->statu->status == 'Disponible') {
                                            if ($dept->classification->stage) {
                                                $deptStage = $dept->classification->stage;
                                                foreach ($deptStage as $value) {
                                                    $getPrice = DmicotizaStagePrice::where('dmicotiza_department_id', $dept->id)
                                                        ->where('stage', $value->stage)
                                                        ->first();
                                                    if ($getPrice) {
                                                        $price = "$ " . number_format($getPrice->price, 2, '.', ', ');
                                                    }
                                                }

                                            }
                                        }
                                        $colorStatus = $this->colorStatus($dept->statu->status);
                                        $infoDept = ['id' => $dept->id,
                                            'colorStatus' => $colorStatus,
                                            'type' => $dept->typeDepartment->type,
                                            'number' => $dept->number,
                                            'm2' => $dept->m2,
                                            'price' => $price,
                                            'level' =>$dept->level,
                                        ];
                                        array_push($infoDepartment, ['info' => true,
                                            'row' => 1,
                                            'column' => 0,
                                            'amenidad' => false,
                                            'background' => $background,
                                            'color' => $color,
                                            'department' => $infoDept,
                                            'subdivision' => $subdivision->id,
                                            'level' => $i,
                                        ]);
                                    } else {
                                        array_push($infoDepartment, ['info' => false,
                                            'row' => 1,
                                            'column' => 0,
                                            'amenidad' => false,
                                            'background' => '#fff',
                                            'color' => '#000',
                                            'subdivision' => $subdivision->id,
                                            'level' => $i,
                                        ]);
                                    }
                                }
                            } else {
                                $cont--;
                            }

                        }
                        array_push($arrayDepartment, ['level' => ($i == 0) ? 'PB' : 'N' . $i, 'department' => $infoDepartment]);
                    }
                    array_push($data, ['name' => $view->view,
                        'viewId' => $view->id,
                        'viewSubdivision' => $get_project->hidden_subdivision,
                        'subdivisionGroups' => $arraySubdivisionGroups,
                        'grupo' => $grupo,
                        'subdivision' => $arraySubdivision,
                        'cantSubdivision' => $view->subdivision,
                        'department' => $arrayDepartment,
                        'projectId' => $get_project->id,
                    ]);
                }
            }
        }
        return $data;
    }

    public function getClassification()
    {
        return DmicotizaClassification::get();
    }

    public function getType($idClassification)
    {
        return DmicotizaTypeDepartment::where('dmicotiza_classification_id',$idClassification)->get();
    }
    public function store(Request $request)
    {
        try {
            if ($request->amedidad) {
                $rules = [
                    'name' =>'required',
                    'row' => 'required',
                    'column' => 'required',
                ];
        
                $messages = [
                    'name.required' => 'El campo nombre es requerido',
                    'row.required' => 'El campo fila es requerido',
                    'column.required' => 'El campo  columna es requerido'
                ];
        
                $this->validate($request, $rules, $messages);
                $amenity=new DmicotizaAmenity();
                $amenity->name=$request->name;
                $amenity->level=$request->level;
                $amenity->column=$request->column;
                $amenity->row=$request->row;
                $amenity->dmicotiza_subdivision_id=$request->subdivision;
                $amenity->dmicotiza_project_view_id=$request->view;
                if($amenity->save()){
                    return response()->json(['success' => 'La amenidad se agregado Correctamente.'], 200);
                }
            } else {
                $rules = [
                    'number' => 'required',
                    'm2' => 'required',
                    'price_m2' => 'required',
                    'type' => 'required',
                    'classification' => 'required',
                ];
        
                $messages = [
                    'number.required' => 'El campo número es requerido',
                    'm2.required' => 'El campo metros cuadrados es requerido',
                    'price_m2.required' => 'El campo precio por metros cuadrado es requerido',
                    'type.required' => 'El campo tipo de departamento es requerido',
                    'classification.required' => 'El campo clasificación del departamento es requerido',
                ];
        
                $this->validate($request, $rules, $messages);
                $statu=DmicotizaStatu::where('status','Disponible')->first();
                $deparment=new DmicotizaDepartment();
                $deparment->number= $request->number;
                $deparment->dmicotiza_type_department_id= $request->type;
                $deparment->level= $request->level;
                $deparment->m2= $request->m2;
                $deparment->price_m2= $request->price_m2;
                $deparment->dmicotiza_classification_id= $request->classification;
                $deparment->dmicotiza_project_id= $request->project;
                $deparment->dmicotiza_project_view_id= $request->view;
                $deparment->drawers= $request->drawers;
                $deparment->dmicotiza_subdivision_id= $request->subdivision;
                $deparment->dmicotiza_statu_id=$statu->id;
                if($deparment->save()){
                    return response()->json(['success' => 'El departamento se agregado Correctamente.'], 200);
                }
            }
            return response()->json(['error' => 'Error al insertar'], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th], 500);
        }
    }
    public function show($id)
    {
        return DmicotizaDepartment::with(['project','projectView',
        'classification','classification.stage' => function ($query) {
            $query->where('dmicotiza_stages.status', '=', DmicotizaStage::ACTIVE);
        },'typeDepartment','statu'])->where('id', $id)->first();
    }

    public function update(Request $request)
    {
        try {
            $rules = [
                'number' => 'required',
                'm2' => 'required',
                'price_m2' => 'required',
                'type' => 'required',
                'classification' => 'required',
            ];
    
            $messages = [
                'number.required' => 'El campo número es requerido',
                'm2.required' => 'El campo metros cuadrados es requerido',
                'price_m2.required' => 'El campo precio por metros cuadrado es requerido',
                'type.required' => 'El campo tipo de departamento es requerido',
                'classification.required' => 'El campo clasificación del departamento es requerido',
            ];
    
            $this->validate($request, $rules, $messages);
            $deparment=DmicotizaDepartment::findOrFail($request->id);
            $deparment->number=$request->number;
            $deparment->drawers=$request->drawers;
            $deparment->m2=$request->m2;
            $deparment->price_m2=$request->price_m2;
            $deparment->dmicotiza_type_department_id=$request->type;
            $deparment->dmicotiza_classification_id=$request->classification;
            if($deparment->update()){
                return response()->json(['success'=>'Se actualizo exitosamente.'],200);
            }
            return response()->json(['error' => 'Error al actualizar'], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th], 500);
        }
    }

    public function updateAmenity(Request $request)
    {
        try {
            $rules = [
                'name' =>'required',
                'row' => 'required',
                'column' => 'required',
            ];
    
            $messages = [
                'name.required' => 'El campo nombre es requerido',
                'row.required' => 'El campo fila es requerido',
                'column.required' => 'El campo  columna es requerido'
            ];
    
            $this->validate($request, $rules, $messages);
            $amenity=DmicotizaAmenity::findOrFail($request->id);
            $amenity->name=$request->name;
            $amenity->column=$request->column;
            $amenity->row=$request->row;
            if($amenity->update()){
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
            $deparment=DmicotizaDepartment::with('statu','stagePrice')->findOrFail($id);
            if($deparment->statu->status="Disponible"){
                if($deparment->delete()){
                    foreach ($deparment->stagePrice as  $value) {
                        $value->delete();
                    }
                    return response()->json(['success'=>'Amenidad eliminada correctamente.'],200);
                }
            }else{
                return response()->json(['error' => 'No se puede eliminar un departamento con estatus: '.$deparment->statu->status], 202);
            }
            
            return response()->json(['error' => 'Error al eliminar'], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th], 500);
        }
    }

    public function destroyAmenity($id)
    {
        try {
            $amenity=DmicotizaAmenity::findOrFail($id);
            if($amenity->delete()){
                return response()->json(['success'=>'Amenidad eliminada correctamente.'],200);
            }
            return response()->json(['error' => 'Error al eliminar'], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th], 500);
        }
        
    }
    public function colorStatus($status)
    {
        switch ($status) {
            case 'Disponible':
                return ['background' => "#01FF81", 'title' => $status];
            case 'Apartado':
                return ['background' => "#FFBB56", 'title' => $status];
            case 'Bloqueado':
                return ['background' => "#FFFFFF", 'title' => $status];
            case 'Vendido':
                return ['background' => "#D90123", 'title' => $status];
        }
    }

    public function statusDeparment(Request $request){ 
        try {
            $deparment=DmicotizaDepartment::findOrFail($request->id);
            $statu=DmicotizaStatu::where('status',$request->status)->first();
            $deparment->dmicotiza_statu_id=$statu->id;
            if($deparment->update()){
                return response()->json(['success'=>'Se actualizo exitosamente.'],200);
            }
            return response()->json(['error' => 'Error al cambiar el estatus'], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th], 500);
        }
    }
}
