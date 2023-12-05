<?php

/**
 * Created by Reliese Model.
 */
namespace App\Models\DmiRh;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DmirhTypePermit
 * 
 * @package App\Models
 */
class DmirhTypePermit extends Model
{
	use SoftDeletes;

	protected $table = 'dmirh_type_permits';

	public $timestamps = true;

	protected $casts = [
		'created_at' => "datetime:Y-m-d h:m:s",
		'updated_at' => 'datetime:Y-m-d h:i:s',
		'deleted_at' => 'datetime:Y-m-d h:i:s',
	];
	
}
