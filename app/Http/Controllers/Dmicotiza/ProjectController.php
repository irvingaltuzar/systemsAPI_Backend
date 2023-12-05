<?php

namespace App\Http\Controllers\Dmicotiza;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dmicotiza\DmicotizaProject;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            $projects=DmicotizaProject::get();
            $data=["projects"=>$projects, "url"=>asset('/')];
            return response()->json($data, 200);
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
                'hitch' => 'required',
                'level' => 'required',
                'project'=> 'required',
            ];
    
            $messages = [
                'hitch.required' => 'El campo enganche es requerido',
                'level.required' => 'El campo niveles es requerido',
                'project.required'=> 'El campo nombre es requerido',
            ];
            $this->validate($request, $rules, $messages);
            $project=new DmicotizaProject();
            $project->project=$request->project;
            $project->hitch=$request->hitch;
            $project->logo='img/logo-dmi.jpeg';
            $project->level=$request->level;
            $project->low_level=$request->low_level;
            $project->hidden_subdivision=$request->hidden_subdivision;
            if($project->save()){
                return response()->json(['success' => 'Se agrego el proyecto exitosamente.'], 200);
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
        return DmicotizaProject::with('stage','stage.classification', 'projectView')->where('id', $id)->first();
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
}
