<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\DmiRh;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DmirhPersonalTimeComment
 * 
 * @property int $id
 * @property int|null $dmirh_personal_time_id
 * @property string $comment
 * @property bool $deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property DmirhPersonalTime|null $dmirh_personal_time
 *
 * @package App\Models
 */
class DmirhPersonalTimeComment extends Model
{
	protected $table = 'dmirh_personal_time_comment';

	protected $casts = [
		'dmirh_personal_time_id' => 'int',
		'deleted' => 'int'
	];

	protected $fillable = [
		'dmirh_personal_time_id',
		'comment',
		'deleted'
	];

	public function dmirh_personal_time()
	{
		return $this->belongsTo(DmirhPersonalTime::class);
	}
}
