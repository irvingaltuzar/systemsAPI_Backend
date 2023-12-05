<?php

/**
 * Created by Reliese Model.
 */
namespace App\Models\DmiRh;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class DmirhPermitConcept
 *
 * @package App\Models
 */
class DmirhPermitConcept extends Model
{
	use SoftDeletes;
	
	protected $table = 'dmirh_permit_concepts';
	
	public $timestamps = true;

	protected $casts = [
		'created_at' => "datetime:Y-m-d h:m:s",
		'updated_at' => 'datetime:Y-m-d h:i:s',
		'deleted_at' => 'datetime:Y-m-d h:i:s',
	];
	
}
