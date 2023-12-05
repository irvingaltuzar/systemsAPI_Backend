<?php

namespace App\Models\Dmihd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmihdPriority extends Model
{
    use SoftDeletes; 
    protected $table = 'dmihd_priorities';
    protected $dates = ['deleted_at'];
}
