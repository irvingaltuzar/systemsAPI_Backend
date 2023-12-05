<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\DmiRh;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DmirhAttendancePolicy
 * 
 * @property int $id
 * @property int $tolerance
 * @property int $delay
 * @property int $puntuality
 * @property int $suspension
 * @property string $location
 * @property bool $deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\DmiRh
 */
class DmirhAttendancePolicy extends Model
{
	protected $table = 'dmirh_attendance_policy';

	protected $casts = [
		'tolerance' => 'int',
		'delay' => 'int',
		'puntuality' => 'int',
		'suspension' => 'int',
		'deleted' => 'bool'
	];

	protected $fillable = [
		'tolerance',
		'delay',
		'puntuality',
		'suspension',
		'location',
		'deleted'
	];
}
