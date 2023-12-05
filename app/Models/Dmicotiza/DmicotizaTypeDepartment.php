<?php

namespace App\Models\Dmicotiza;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmicotizaTypeDepartment extends Model
{
    use SoftDeletes; 
    protected $table = 'dmicotiza_type_departments';
    protected $dates = ['deleted_at'];

    public function department()
    {
        return $this->belongsTo('App\Models\Dmicotiza\DmicotizaDepartment','id', 'dmicotiza_type_department_id');
    }
}
