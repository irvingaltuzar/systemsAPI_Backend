<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class FoodMenu extends Model
{
	use SoftDeletes;

	protected $guarded = [];

	protected $appends = ['first_image_url'];

	protected $table = 'dmirh_food_menus';

	// protected $dates = [
    //     'start_date',
	// 	'finish_date'
    // ];

	public function setEnabledDaysAttribute($value)
	{
		$this->attributes['enabled_days'] = serialize($value);
	}

	public function getEnabledDaysAttribute($value)
	{
		return unserialize($value);
	}

	public function image()
	{
		return $this->hasOne(FoodMenuFile::class);
	}

	public function location()
	{
		return $this->belongsTo(Location::class);
	}

	public function getFirstImageUrlAttribute()
	{
		return Storage::disk('Publico')->url("Comedor/{$this->id}/{$this->image->file_menu}");
	}

	public function orders()
	{
		return $this->hasMany(FoodOrder::class);
	}
}
