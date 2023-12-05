<?php

namespace App\Models\Dmicotiza;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DmicotizaAmenity extends Model
{
    use SoftDeletes; 
    protected $table = 'dmicotiza_amenities';
    protected $dates = ['deleted_at'];

    public function projectViews()
    {
        return $this->hasMany('App\Models\Dmicotiza\DmicotizaProjectView','id','dmicotiza_project_view_id');
    }
}
