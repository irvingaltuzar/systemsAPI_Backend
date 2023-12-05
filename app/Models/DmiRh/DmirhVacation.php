<?php

/**
 * Created by Reliese Model.
 */
namespace App\Models\DmiRh;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PersonalIntelisis;
use App\Models\DmiControlSignaturesBehalfAudit;
use App\Models\DmiBucketSignature;
/**
 * Class DmirhWorkPermit
 *
 * @package App\Models
 */
class DmirhVacation extends Model
{
	use SoftDeletes;

	protected $table = 'dmirh_vacation';

	public $timestamps = true;

	protected $casts = [
		'start_date' => 'datetime:d-m-Y',
		'end_date' => 'datetime:d-m-Y',
		'return_date' => 'datetime:d-m-Y',
		'date_request' => 'datetime:d-m-Y',
		'created_at' => 'datetime:d-m-Y h:i:s',
		'updated_at' => 'datetime:d-m-Y h:i:s',
		'deleted_at' => 'datetime:d-m-Y h:i:s',

	];

	protected $appends = [
		'signatures',
		'api_document',
		'sign_behalf',
	];

	public function personal_intelisis(){
		return $this->belongsTo(PersonalIntelisis::class,'personal_intelisis_usuario_ad','usuario_ad')->with('immediate_boss')->where('status','ALTA');
	}

	public function getSignaturesAttribute(){
		$signatures = DmiBucketSignature::with('personal_intelisis')->where('origin_record_id',$this->id)->where('seg_seccion_id',11)->get();
		return $signatures;
	}
	public function getApiDocumentAttribute(){
		return url($this->document);
	}

	public function getSignBehalfAttribute(){
		return DmiControlSignaturesBehalfAudit::with(['personal_intelisis'])->where('origin_record_id',$this->id)->where('seg_seccion_id',11)->first();

	}



}
