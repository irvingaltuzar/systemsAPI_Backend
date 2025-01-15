<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DmiControlEmailDomain;
use App\Models\DmiCatStatusRecruitment;
use App\Models\PersonalIntelisis;
use Storage;
use App\Models\VwDmiPersonalPlaza;
/**
 * Class DmirhPersonalRequisition
 * 
 * @property int $id
 * @property string $type
 * @property string $department
 * @property Carbon $date
 * @property Carbon|null $date_validation_rh
 * @property Carbon|null $date_received_rh
 * @property Carbon|null $date_estimate_coverage
 * @property string $vacancy
 * @property string $type_vacancy
 * @property string $time_travel
 * @property int $days_travel
 * @property string $user
 * @property float $salary
 * @property string|null $estimate
 * @property string $mail_location
 * @property string|null $resources
 * @property string $personal_location
 * @property string|null $file
 * @property string|null $bussiness_name
 * @property int|null $branch_office
 * @property string|null $status
 * @property string|null $temp_reason
 * @property int|null $days_temp_reason
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models
 */
class DmirhPersonalRequisition extends Model
{
	use SoftDeletes;
	protected $table = 'dmirh_personal_requisition';

	protected $casts = [
		'days_travel' => 'int',
		'salary' => 'float',
		'branch_office' => 'int',
		'days_temp_reason' => 'int',
		'date' => 'date:d-m-Y',
		'date_validation_rh' => 'date:d-m-Y',
		'date_received_rh' => 'date:d-m-Y',
		'date_estimate_coverage' => 'date:d-m-Y',
		'created_at' => 'datetime:Y-m-d h:i:s',
		'updated_at' => 'datetime:Y-m-d h:i:s',
		'deleted_at' => 'datetime:Y-m-d h:i:s',
	];

	protected $dates = [
		'date',
		'date_validation_rh',
		'date_received_rh',
		'date_estimate_coverage'
	];

	protected $fillable = [
		'type',
		'department',
		'date',
		'date_validation_rh',
		'date_received_rh',
		'date_estimate_coverage',
		'vacancy',
		'personal_substitution',
		'type_vacancy',
		'time_travel',
		'days_travel',
		'user',
		'salary',
		'estimate',
		'mail_location',
		'resources',
		'personal_location',
		'file',
		'bussiness_name',
		'branch_office',
		'status',
		'status_recruitment_id',
		'temp_reason',
		'days_temp_reason',
		'document'
	];
	protected $appends = ['file_url', 'document_url'];

	public function setResourcesAttribute($value)

    {
        $this->attributes['resources'] = serialize($value);
    }

    public function getResourcesAttribute($value)
    {
        return unserialize($value);
    }

    public function dmi_control_email_domain()
	{
		return $this->belongsTo(DmiControlEmailDomain::class, "email_domain_id");
	}
	public function dmi_cat_status_recruitment()
	{
		return $this->belongsTo(DmiCatStatusRecruitment::class, "status_recruitment_id");
	}

	public function personal_intelisis(){
		return $this->belongsTo(PersonalIntelisis::class,'user','usuario_ad');
	}
	public function personal_intelisis_plaza(){
		return $this->belongsTo(VwDmiPersonalPlaza::class,'plaza_id','plaza_id');
	}
	
	public function dmi_personal_substitution(){
		return $this->belongsTo(PersonalIntelisis::class,'personal_substitution','personal_id');
	}

	public function getSignaturesAttribute(){
		$signatures = DmiBucketSignature::with('personal_intelisis')->where('origin_record_id',$this->id)->where('seg_seccion_id',12)->get();
		return $signatures;
	}

	public function getFileUrlAttribute()
	{
		return Storage::disk('public')->url("Requisitions/{$this->file}");
	}
	public function getdocumentUrlAttribute()
	{
		return Storage::disk('public')->url("Requisitions/{$this->document}");
	}
}
