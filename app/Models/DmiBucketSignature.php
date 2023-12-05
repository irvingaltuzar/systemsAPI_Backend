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
 * Class DmiBucketSignature
 *
 * @package App\Models
 */
class DmiBucketSignature extends Model
{
	use SoftDeletes;

	protected $table = 'dmi_bucket_signatures';

	public $timestamps = true;

	protected $casts = [
		'order' => 'int',
		//'signed_date' => 'datetime:d-m-Y h:i:s',
		'created_at' => "datetime:Y-m-d h:i:s",
		'updated_at' => 'datetime:Y-m-d h:i:s',
		'deleted_at' => 'datetime:Y-m-d h:i:s',
	];

	public function personal_intelisis(){
		return $this->belongsTo(PersonalIntelisis::class,'personal_intelisis_usuario_ad','usuario_ad')->where('status','ALTA');
	}

	public function work_permit(){
		return $this->belongsTo(DmirhWorkPermit::class,'origin_record_id','id');
	}

}
