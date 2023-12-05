<?php

namespace App\Models\Dmihd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmihdArea extends Model
{
    use SoftDeletes;
    protected $table = 'dmihd_areas';
    protected $dates = ['deleted_at'];
    public function location()
    {
        return $this->hasOne('App\Models\Dmihd\DmihdLocation','id','dmihd_location_id');
    }
}