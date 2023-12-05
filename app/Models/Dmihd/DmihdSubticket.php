<?php

namespace App\Models\Dmihd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmihdSubticket extends Model
{
    use SoftDeletes; 
    protected $table = 'dmihd_sub_tickets';
    protected $dates = ['deleted_at'];
}
