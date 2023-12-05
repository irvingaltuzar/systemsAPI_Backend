<?php

namespace App\Models\Dmicotiza;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmicotizaStage extends Model
{
    use SoftDeletes; 
    protected $table = 'dmicotiza_stages';
    protected $dates = ['deleted_at'];
    const ACTIVE = 1;
    const INACTIVE = 0;

    public function projects()
    {
        return $this->hasMany('App\Models\Dmicotiza\DmicotizaProject', 'id','dmicotiza_project_id');
    }
    public function classification()
    {
        return $this->hasOne('App\Models\Dmicotiza\DmicotizaClassification','id', 'dmicotiza_classification_id');
    }
}
