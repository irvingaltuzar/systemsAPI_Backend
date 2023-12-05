<?php

namespace App\Models\Dmihd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmihdUserTicket extends Model
{
    use SoftDeletes; 
    protected $table = 'dmihd_user_tickets';
    protected $dates = ['deleted_at'];
    public function subArea()
    {
        return $this->hasOne('App\Models\Dmihd\DmihdSubArea','id','dmihd_sub_area_id');
    }
}
