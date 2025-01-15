<?php

namespace App\Models\DmiRh;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmirhIncidentProcessMovERP extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'dmirh_incident_process_mov_erp';

    public $timestamps = true;

	protected $casts = [
		'created_at' => 'datetime:d-m-Y h:i:s',
		'updated_at' => 'datetime:d-m-Y h:i:s',
		'deleted_at' => 'datetime:d-m-Y h:i:s',

	];

	protected $appends = [];
}
