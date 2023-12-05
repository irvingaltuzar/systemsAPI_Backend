<?php

/**
 * Created by Reliese Model.
 */
namespace App\Models\DmiRh;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PersonalIntelisis;
use App\Models\DmiBucketSignature;
/**
 * Class DmirhWorkPermit
 * 
 * @package App\Models
 */
class DmirhTempTopSustitution extends Model
{
	use SoftDeletes;

	protected $table = 'dmirh_temp_top_sustitution';

	public $timestamps = true;

	protected $casts = [
		'created_at' => "datetime:Y-m-d h:m:s",
		'updated_at' => 'datetime:Y-m-d h:i:s',
		'deleted_at' => 'datetime:Y-m-d h:i:s',
		
	];


}
