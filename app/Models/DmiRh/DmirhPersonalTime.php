<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\DmiRh;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DmirhPersonalTime
 *
 * @property int $id
 * @property string $user
 * @property int $dmirh_cat_time_status_id
 * @property Carbon $start_date
 * @property string $approved_by
 * @property Carbon $approved_date
 * @property bool $deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property DmirhCatTimeStatus $dmirh_cat_time_status
 * @property Collection|DmirhPersonalTimeComment[] $dmirh_personal_time_comments
 * @property Collection|DmirhPersonalTimeDetail[] $dmirh_personal_time_details
 *
 * @package App\Models
 */
class DmirhPersonalTime extends Model
{
	protected $table = 'dmirh_personal_time';

	protected $casts = [
		'dmirh_cat_time_status_id' => 'int',
		'deleted' => 'int',
		'created_at' => "datetime:Y-m-d h:m:s",
		'updated_at' => 'datetime:Y-m-d h:i:s',
	];

	protected $dates = [
		'start_date',
		'approved_date'
	];

	protected $fillable = [
		'user',
		'dmirh_cat_time_status_id',
		'start_date',
		'active',
		'approved_by',
		'approved_date',
		'deleted'
	];

	protected $appends=[
		'hours_week'
	];

	public function dmirh_cat_time_status()
	{
		return $this->belongsTo(DmirhCatTimeStatus::class);
	}

	public function dmirh_personal_time_comments()
	{
		return $this->hasMany(DmirhPersonalTimeComment::class);
	}

	public function dmirh_personal_time_details()
	{
		return $this->hasMany(DmirhPersonalTimeDetail::class)
				->where('deleted',0)
				->orderBy('week_day');
	}

	public function getHoursWeekAttribute(){
		return  DmirhPersonalTimeDetail::where('dmirh_personal_time_id',$this->dmirh_personal_time_id)->where('deleted',0)->sum('hours_day');
	}
}
