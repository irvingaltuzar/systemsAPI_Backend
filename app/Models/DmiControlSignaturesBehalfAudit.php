<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmiControlSignaturesBehalfAudit extends Model
{
    use HasFactory;
	use SoftDeletes;

	protected $table = 'dmicontrol_signatures_behalves_audit';

	public $timestamps = true;

	protected $casts = [
		'created_at' => "datetime:Y-m-d h:m:s",
		'updated_at' => 'datetime:Y-m-d h:i:s',
		'deleted_at' => 'datetime:Y-m-d h:i:s',
	];

	public function personal_intelisis(){
		return $this->hasOne(PersonalIntelisis::class,'usuario_ad','sign_behalf_usuario_ad')->where('status','ALTA');
	}

	public function personal_intelisis_requisition(){
		return $this->belongsTo(PersonalIntelisis::class,'sign_behalf_usuario_ad','usuario_ad');
	}
}
