<?php

namespace App\Models\Dmicotiza;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmicotizaPlan extends Model
{
    use SoftDeletes; 
    protected $table = 'dmicotiza_plans';
    protected $dates = ['deleted_at'];

    public function typePlan()
    {
        return $this->hasOne('App\Models\Dmicotiza\DmicotizaTypePlan','id', 'dmicotiza_type_plan_id');
    }
    public function projects()
    {
        return $this->belongsToMany('App\Models\Dmicotiza\DmicotizaProject','dmicotiza_plan_projects');
    }
}
