<?php

namespace App\Http\Controllers\Dmicotiza;

use App\Http\Controllers\Controller;
use App\Models\Dmicotiza\DmicotizaStage;
use Illuminate\Http\Request;

class StageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
                'quantity_department' => 'required',
                'classification' => 'required',
                'stage' => 'required',
            ];

            $messages = [
                'quantity_department.required' => 'El campo cantidad de departamentos es requerido',
                'classification.required' => 'El campo clasificación es requerido',
                'stage.required' => 'El campo etapa es requerido',
            ];
            $this->validate($request, $rules, $messages);
            $project = new DmicotizaStage();
            $project->stage = $request->stage;
            $project->quantity_department = $request->quantity_department;
            $project->status = ($request->stage == 0) ? 1 : 0;
            $project->dmicotiza_classification_id = $request->classification;
            $project->dmicotiza_project_id = $request->project;
            if ($project->save()) {
                return response()->json(['success' => 'Se agrego la etapa exitosamente.'], 200);
            }
            return response()->json(['error' => 'Error al insertar'], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function stage($classification, $project)
    {
        return DmicotizaStage::where('dmicotiza_project_id', $project)->where('dmicotiza_classification_id', $classification)->count();
    }
}
