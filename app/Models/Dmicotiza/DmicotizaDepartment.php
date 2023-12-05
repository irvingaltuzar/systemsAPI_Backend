<?php

namespace App\Models\Dmicotiza;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmicotizaDepartment extends Model
{
    use SoftDeletes; 
    protected $table = 'dmicotiza_departments';
    protected $dates = ['deleted_at'];

    public function classification()
    {
        return $this->hasOne('App\Models\Dmicotiza\DmicotizaClassification','id','dmicotiza_classification_id');
    }
    public function projectView()
    {
        return $this->hasOne('App\Models\Dmicotiza\DmicotizaProjectView','id','dmicotiza_project_view_id');
    }
    public function typeDepartment()
    {
        return $this->hasOne('App\Models\Dmicotiza\DmicotizaTypeDepartment','id','dmicotiza_type_department_id');
    }
    public function project()
    {
        return $this->hasOne('App\Models\Dmicotiza\DmicotizaProject','id','dmicotiza_project_id');
    }
    public function stagePrice()
    {
        return $this->hasMany('App\Models\Dmicotiza\DmicotizaStagePrice','dmicotiza_department_id','id');
    }
    public function statu()
    {
        return $this->hasOne('App\Models\Dmicotiza\DmicotizaStatu','id','dmicotiza_statu_id');
    }
}
