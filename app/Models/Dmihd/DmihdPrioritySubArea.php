<?php

namespace App\Models\Dmihd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmihdPrioritySubArea extends Model
{
    use SoftDeletes; 
    protected $table = 'dmihd_priority_sub_areas';
    protected $dates = ['deleted_at'];
    public function subArea()
    {
        return $this->hasOne('App\Models\Dmihd\DmihdSubArea','id','dmihd_sub_area_id');
    }

    public function priority()
    {
        return $this->hasOne('App\Models\Dmihd\DmihdPriority','id','dmihd_priority_id');
    }
}
