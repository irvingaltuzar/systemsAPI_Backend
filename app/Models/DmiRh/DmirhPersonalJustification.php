<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\DmiRh;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PersonalIntelisis;

/**
 * Class DmirhPersonalJustification
 * 
 * @property int $id
 * @property int $type_id
 * @property string $description
 * @property string $file
 * @property Carbon $date
 * @property string $user
 * @property string $approved_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property DmirhCatTypeJustification $dmirh_cat_type_justification
 *
 * @package App\Models\DmiRh
 */
class DmirhPersonalJustification extends Model
{
	use SoftDeletes;
	
	protected $table = 'dmirh_personal_justification';

	public $timestamps = true;

	protected $casts = [
		'type_id' => 'int',
		'date' => 'datetime:d-m-Y',
		'created_at' => 'datetime:d-m-Y h:i:s',
		'updated_at' => 'datetime:d-m-Y h:i:s',
		'deleted_at' => 'datetime:d-m-Y h:i:s',
	];

	protected $dates = [
		'date',
		'deleted_at'
	];

	protected $fillable = [
		'type_id',
		'description',
		'file',
		'date',
		'user',
		'approved_by',
		'created_at',
		'updated_at',
		'deleted_at'
	];
	protected $appends = ['file_url', 'document_url'];

	public function dmirh_cat_type_justification()
	{
		return $this->belongsTo(DmirhCatTypeJustification::class, 'type_id');
	}
	public function getFileUrlAttribute()
	{
		return Storage::disk('public')->url("justifications/{$this->file}");
	}
	public function getdocumentUrlAttribute()
	{
		return Storage::disk('public')->url("justifications/{$this->document}");
	}
	public function personal_intelisis(){
		return $this->belongsTo(PersonalIntelisis::class,'user','usuario_ad')->where('status',"ALTA");
	}
}
