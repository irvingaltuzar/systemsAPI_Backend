<?php

namespace App\Models\Dmicotiza;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class DmicotizaClassification extends Model
{
    use SoftDeletes; 
    protected $table = 'dmicotiza_classifications';
    protected $dates = ['deleted_at'];
    
    public function department()
    {
        return $this->belongsTo('App\Models\Dmicotiza\DmicotizaDepartment', 'id', 'dmicotiza_classification_id');
    }
    public function stage()
    {
        return $this->hasMany('App\Models\Dmicotiza\DmicotizaStage',  'dmicotiza_classification_id', 'id');
    }
}
