<?php

/**
 * Created by Reliese Model.
 */
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\DmiRh\DmirhWorkPermit;


/**
 * Class DmiControlProcedureValidation
 *
 * @package App\Models
 */
class DmiControlProcedureValidation extends Model
{
	use SoftDeletes;

	protected $table = 'dmicontrol_procedure_validation';

	public $timestamps = true;

	protected $casts = [
		'created_at' => "datetime:Y-m-d h:i:s",
		'updated_at' => 'datetime:Y-m-d h:i:s',
		'deleted_at' => 'datetime:Y-m-d h:i:s',
	];

}
