<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DmiRhFoodOrderProduct extends Model
{
	use SoftDeletes;

	protected $guarded = [];

	protected $table = 'dmirh_food_order_products';

	protected $appends = ['food_type', 'day_name'];

	protected $hidden = ['type'];

	public function getFoodTypeAttribute()
	{
		return $this->type->description;
	}

	public function getDayNameAttribute()
	{
		return $this->workDay->description;
	}

	public function type()
	{
		return $this->belongsTo(CatFoodType::class, 'food_type_id');
	}

	public function workDay()
	{
		return $this->belongsTo(WorkDays::class, 'work_day_id');
	}

	public function order()
	{
		return $this->belongsTo(FoodOrder::class, 'food_order_id');
	}
}
