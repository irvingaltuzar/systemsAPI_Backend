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
use App\Models\BucketFiles;
use Illuminate\Support\Facades\DB;
/**
 * Class DmirhWorkPermit
 *
 * @package App\Models
 */
class DmirhWorkPermit extends Model
{
	use SoftDeletes;

	protected $table = 'dmirh_work_permits';

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
		'sign_behalf_direct',
		'attach_document',
	];


	public function type_permit(){
		return $this->belongsTo(DmirhTypePermit::class,'dmirh_type_permits_id','id');
	}

	public function permit_concept(){
		return $this->belongsTo(DmirhPermitConcept::class,'dmirh_permit_concepts_id','id')->withTrashed();
	}

	public function personal_intelisis(){
		return $this->belongsTo(PersonalIntelisis::class,'personal_intelisis_usuario_ad','usuario_ad')->where('status',"ALTA");
	}

	public function getSignaturesAttribute(){
		$signatures = DmiBucketSignature::with('personal_intelisis')->where('origin_record_id',$this->id)->where('seg_seccion_id',9)->get();
		return $signatures;
	}
	public function getApiDocumentAttribute(){
		return url($this->document);
	}

	public function getSignBehalfAttribute(){
		return DmiControlSignaturesBehalfAudit::with('personal_intelisis')
												->where('origin_record_id',$this->id)
												->where('signature_order',2)
												->where('seg_seccion_id',9)->first();

	}

	public function getSignBehalfDirectAttribute(){
		return DmiControlSignaturesBehalfAudit::with('personal_intelisis')
												->where('origin_record_id',$this->id)
												->where('signature_order',3)
												->where('seg_seccion_id',9)->first();

	}

	public function getAttachDocumentAttribute(){

		$url = url("");

		$attach_document = BucketFiles::join('files','files.id','bucket_files.file_id')
										->where('bucket_files.seg_seccion_id',9)
										->where('bucket_files.origin_record_id',$this->id)
										->whereNull('bucket_files.deleted_at')
										->whereNull('files.deleted_at')
										//->selectRaw('files.*,concat('.url().',files.url) as api_document')
										->select('files.*',DB::raw("concat('$url/',files.url) as api_document"))
										->get();

		return $attach_document;

	}



}
