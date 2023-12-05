<?php

namespace App\Models\Dmicotiza;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmicotizaProjectView extends Model
{
    use SoftDeletes; 
    protected $table = 'dmicotiza_project_views';
    protected $dates = ['deleted_at'];

    public function department()
    {
        return $this->belongsTo('App\Models\Dmicotiza\DmicotizaDepartment','dmicotiza_project_view_id','id');
    }
    public function projects()
    {
        return $this->hasMany('App\Models\Dmicotiza\DmicotizaProject','id','dmicotiza_project_id');
    }
    public function subdivisions()
    {
        return $this->hasMany('App\Models\Dmicotiza\DmicotizaSubdivision','dmicotiza_project_view_id','id');
    }
    public function amenities()
    {
        return $this->hasMany('App\Models\Dmicotiza\DmicotizaAmenity','dmicotiza_project_view_id','id');
    }
}
