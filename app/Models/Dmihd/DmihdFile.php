<?php

namespace App\Models\Dmihd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmihdFile extends Model
{
    use SoftDeletes; 
    protected $table = 'dmihd_files';
    protected $dates = ['deleted_at'];
}
