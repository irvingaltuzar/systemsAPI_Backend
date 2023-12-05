<?php

namespace App\Models\Dmihd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmihdTicket extends Model
{
    use SoftDeletes; 
    protected $table = 'dmihd_tickets';
    protected $dates = ['deleted_at'];
    public function prioritySubArea()
    {
        return $this->hasOne('App\Models\Dmihd\DmihdPrioritySubArea','id','dmihd_priority_sub_area_id');
    }
    public function subArea()
    {
        return $this->hasOne('App\Models\Dmihd\DmihdSubArea','id','dmihd_sub_area_id');
    }
    public function ticketStatuse()
    {
        return $this->hasOne('App\Models\Dmihd\DmihdTicketStatuse','id','dmihd_ticket_statuses_id');
    }
    public function status()
    {
        return $this->hasOne('App\Models\Dmihd\DmihdStatu','id','dmihd_status_id');
    }
    public function participants()
    {
        return $this->hasMany('App\Models\Dmihd\DmihdParticipant',  'dmihd_ticket_id');
    }
}

