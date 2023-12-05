<?php

namespace App\Models\Dmihd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmihdFileSubticket extends Model
{
    use SoftDeletes; 
    protected $table = 'dmihd_file_sub_tickets';
    protected $dates = ['deleted_at'];
}
