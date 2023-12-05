<?php

namespace App\Models\Dmihd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmihdTicketStatuse extends Model
{
    use SoftDeletes; 
    protected $table = 'dmihd_ticket_statuses';
    protected $dates = ['deleted_at'];
}
