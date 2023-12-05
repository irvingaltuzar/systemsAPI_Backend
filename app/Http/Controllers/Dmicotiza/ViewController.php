<?php

namespace App\Http\Controllers\Dmicotiza;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dmicotiza\DmicotizaProjectView;

class ViewController extends Controller
{

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
                'position' => 'required',
                'name' => 'required',
                'subdivision'=> 'required',
            ];
    
            $messages = [
                'position.required' => 'El campo posición es requerido',
                'name.required' => 'El campo nombre es requerido',
                'subdivision.required'=> 'El campo subdivisiones es requerido',
            ];
            $this->validate($request, $rules, $messages);
            $projectView=new DmicotizaProjectView();
            $projectView->view=$request->name;
            $projectView->position=$request->position;
            $projectView->subdivision=$request->subdivision;
            $projectView->dmicotiza_project_id=$request->project;
            if($projectView->save()){
                return response()->json(['success' => 'Se agrego la vista exitosamente.'], 200);
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
        return DmicotizaProjectView::with(['subdivisions' => function ($query) {
            $query->orderBy('position');
        },'subdivisions.subdivisionGroup'])->where('id',$id)->first();
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
