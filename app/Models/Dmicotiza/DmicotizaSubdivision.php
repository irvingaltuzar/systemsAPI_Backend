<?php

namespace App\Models\Dmicotiza;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DmicotizaSubdivision extends Model
{
    use SoftDeletes; 
    protected $table = 'dmicotiza_subdivisions';
    protected $dates = ['deleted_at'];
    public function projectViews()
    {
        return $this->hasMany('App\Models\Dmicotiza\DmicotizaProjectView','id','dmicotiza_project_view_id');
    }
    public function subdivisionGroup()
    {
        return $this->hasOne('App\Models\Dmicotiza\DmicotizaSubdivisionGroup','dmicotiza_subdivision_id','id');
    }
}
