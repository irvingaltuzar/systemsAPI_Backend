<?php

namespace App\Models\Dmicotiza;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmicotizaTypePlan extends Model
{
    use SoftDeletes; 
    protected $table = 'dmicotiza_type_plans';
    protected $dates = ['deleted_at'];

    public function plan()
    {
        return $this->belongsTo('App\Models\Dmicotiza\DmicotizaPlan','dmicotiza_type_plan_id','id');
    }
}
