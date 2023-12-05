<?php

namespace App\Models\Dmicotiza;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmicotizaSubdivisionGroup extends Model
{
    use SoftDeletes; 
    protected $table = 'dmicotiza_subdivision_groups';
    protected $dates = ['deleted_at'];
}
