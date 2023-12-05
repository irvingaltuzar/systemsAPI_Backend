<?php

namespace App\Models\Dmicotiza;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DmicotizaStatu extends Model
{
    use SoftDeletes; 
    protected $table = 'dmicotiza_status';
    protected $dates = ['deleted_at'];
}
