<?php

namespace App\Models\Dmihd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class DmihdLocation extends Model
{
    use SoftDeletes; 
    protected $table = 'dmihd_locations';
    protected $dates = ['deleted_at'];
}
