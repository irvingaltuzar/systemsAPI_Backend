<?php

namespace App\Models\Dmicotiza;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmicotizaProject extends Model
{
    use SoftDeletes; 
    protected $table = 'dmicotiza_projects';
    protected $dates = ['deleted_at'];

    public function plans()
    {
        return $this->belongsToMany('App\Models\Dmicotiza\DmicotizaPlan','dmicotiza_plan_projects');
    }
    public function department()
    {
        return $this->belongsTo('App\Models\Dmicotiza\DmicotizaDepartment','dmicotiza_project_id','id');
    }
    public function projectView()
    {
        return $this->hasMany('App\Models\Dmicotiza\DmicotizaProjectView','dmicotiza_project_id','id');
    }
    public function stage()
    {
        return $this->hasMany('App\Models\Dmicotiza\DmicotizaStage','dmicotiza_project_id','id');
    }
}
