<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\DmiRh;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DmirhWorkSchedule
 * 
 * @property int $id
 * @property Carbon $hour
 * @property string|null $description
 * @property string $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class DmirhWorkSchedule extends Model
{
	protected $table = 'dmirh_work_schedules';

	protected $dates = [
		'hour'
	];

	protected $fillable = [
		'hour',
		'description',
		'type'
	];
	protected $casts = [
		'hour' => 'datetime:H:i',
	];
}
