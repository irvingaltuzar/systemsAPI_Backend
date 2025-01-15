<?php

/**
 * Created by Reliese Model.
 */
namespace App\Models\DmiRh;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DmirhPersonalTimeDetail
 *
 * @property int $id
 * @property int|null $dmirh_personal_time_id
 * @property int $week_day
 * @property Carbon $entry_hour
 * @property Carbon $exit_food_hour
 * @property Carbon $entry_food_hour
 * @property Carbon $exit_hour
 * @property bool $deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property DmirhPersonalTime|null $dmirh_personal_time
 *
 * @package App\Models
 */
class DmirhPersonalTimeDetail extends Model
{
	protected $table = 'dmirh_personal_time_detail';

	protected $casts = [
		'dmirh_personal_time_id' => 'int',
		'week_day' => 'int',
		'deleted' => 'int',
		'created_at' => "datetime:Y-m-d h:i:s",
		'updated_at' => 'datetime:Y-m-d h:i:s',
	];

	/* protected $dates = [
		'entry_hour',
		'exit_food_hour',
		'entry_food_hour',
		'exit_hour'
	]; */

	protected $fillable = [
		'dmirh_personal_time_id',
		'week_day',
		'entry_hour',
		'exit_food_hour',
		'entry_food_hour',
		'exit_hour',
		'deleted'
	];

	public function dmirh_personal_time()
	{
		return $this->belongsTo(DmirhPersonalTime::class);
	}
}
