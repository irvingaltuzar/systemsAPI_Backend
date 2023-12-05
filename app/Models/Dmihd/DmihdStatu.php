<?php

namespace App\Models\Dmihd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmihdStatu extends Model
{
    use SoftDeletes; 
    protected $table = 'dmihd_status';
    protected $dates = ['deleted_at'];
}
