<?php

namespace App\Models\Dmihd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmihdParticipant extends Model
{
    use SoftDeletes; 
    protected $table = 'dmihd_participants';
    protected $dates = ['deleted_at'];
}
