<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\DmiRh;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DmirhCatTimeStatus
 * 
 * @property int $id
 * @property string $description
 * @property bool $deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|DmirhPersonalTime[] $dmirh_personal_times
 *
 * @package App\Models
 */
class DmirhCatTimeStatus extends Model
{
	protected $table = 'cat_time_status';

	protected $casts = [
		'deleted' => 'int'
	];

	protected $fillable = [
		'description',
		'deleted'
	];

	public function dmirh_personal_times()
	{
		return $this->hasMany(DmirhPersonalTime::class);
	}
}
