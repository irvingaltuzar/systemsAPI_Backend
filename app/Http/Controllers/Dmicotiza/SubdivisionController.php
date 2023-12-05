<?php

namespace App\Http\Controllers\Dmicotiza;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dmicotiza\DmicotizaSubdivision;

class SubdivisionController extends Controller
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
                'position' => 'required',
                'name' => 'required',
                'view' => 'required',
            ];

            $messages = [
                'position.required' => 'El campo posición es requerido',
                'name.required' => 'El campo nombre es requerido',
                'view.required' => 'El campo id de la vista es requerido',
            ];
            $this->validate($request, $rules, $messages);
            $project = new DmicotizaSubdivision();
            $project->position = $request->position;
            $project->name = $request->name;
            $project->dmicotiza_project_view_id = $request->view;
            if ($project->save()) {
                return response()->json(['success' => 'Se agrego la subdivisión exitosamente.'], 200);
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
}
