<?php

/**
 * Created by Reliese Model.
 */
namespace App\Models\DmiRh;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PersonalIntelisis;
use App\Models\DmiRh\DmirhPersonalJustification;
/**
 * Class DmirhIncidentProcess
 *
 * @package App\Models
 */
class DmirhIncidentProcessDetail extends Model
{
	use SoftDeletes;

	protected $table = 'dmirh_incident_process_detail';

	public $timestamps = true;

	protected $casts = [
		'created_at' => 'datetime:d-m-Y h:i:s',
		'updated_at' => 'datetime:d-m-Y h:i:s',
		'deleted_at' => 'datetime:d-m-Y h:i:s',

	];

	protected $appends = [
		"check_format"
	];

	public function getCheckFormatAttribute(){
		return json_decode($this->check_registers);
	}

	public function personal_justification(){
		return $this->hasOne(DmirhPersonalJustification::class,'id','dmirh_personal_justification_id');
	}


	



}
