<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class AuditAutomatedProcess extends Model
{
    use SoftDeletes;

    protected $table = 'audit_automated_processes';

    public $timestamps = true;

    protected $casts = [
		'created_at' => 'datetime:Y-m-d h:i:s',
		'updated_at' => 'datetime:Y-m-d h:i:s',
		'deleted_at' => 'datetime:Y-m-d h:i:s',
	];
}
