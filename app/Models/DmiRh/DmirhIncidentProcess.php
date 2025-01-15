<?php

/**
 * Created by Reliese Model.
 */
namespace App\Models\DmiRh;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PersonalIntelisis;
use App\Models\DmiRh\DmirhIncidentProcessDetail;
/**
 * Class DmirhIncidentProcess
 *
 * @package App\Models
 */
class DmirhIncidentProcess extends Model
{
	use SoftDeletes;

	protected $table = 'dmirh_incident_process';

	public $timestamps = true;

	protected $casts = [
		'start_date' => 'datetime:d-m-Y',
		'end_date' => 'datetime:d-m-Y',
		'created_at' => 'datetime:d-m-Y h:i:s',
		'updated_at' => 'datetime:d-m-Y h:i:s',
		'deleted_at' => 'datetime:d-m-Y h:i:s',

	];

	protected $appends = [
		"incident_process_detail_justified",
		"collaborators_contemplated"
	];


	public function personal_intelisis(){
		return $this->hasOne(PersonalIntelisis::class,'rfc','rfc_generated');
	}

	public function incident_process_detail(){
		return $this->hasMany(DmirhIncidentProcessDetail::class,'dmirh_incident_process_id','id');
	}

	public function getIncidentProcessDetailJustifiedAttribute(){
		return DmirhIncidentProcessDetail::where('dmirh_incident_process_id',$this->id)->whereNotNull('dmirh_personal_justification_id')->count();
	}
	public function getCollaboratorsContemplatedAttribute(){
		return (sizeof(json_decode($this->collaborators_contemplated_rfc)));
	}
	
}
