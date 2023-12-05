<?php

namespace App\Models\Dmicotiza;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmicotizaStagePrice extends Model
{
    use SoftDeletes; 
    protected $table = 'dmicotiza_stage_prices';
    protected $dates = ['deleted_at'];

    public function department()
    {
        return $this->hasOne('App\Models\Dmicotiza\DmicotizaDepartment','id','dmicotiza_department_id');
    }
}
