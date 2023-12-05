<?php

namespace App\Models\Dmihd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmihdSubArea extends Model
{
    use SoftDeletes; 
    protected $table = 'dmihd_sub_areas';
    protected $dates = ['deleted_at'];
    public function area()
    {
        return $this->hasOne('App\Models\Dmihd\DmihdArea','id','dmihd_area_id');
    }
}
