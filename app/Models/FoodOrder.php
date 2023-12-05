<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FoodOrder extends Model
{
	use SoftDeletes;

	protected $guarded = [];

	protected $table = 'dmirh_food_orders';

	protected $appends = ['user_name'];

	public function getUserNameAttribute()
	{
		return "{$this->user->nombre} {$this->user->apePat} {$this->user->apeMat}";
	}

	public function products()
	{
		return $this->hasMany(DmiRhFoodOrderProduct::class);
	}

	public function menu()
	{
		return $this->belongsTo(FoodMenu::class, 'food_menu_id');
	}

	public function user()
	{
		return $this->belongsTo(SegUsuario::class, 'seg_usuario_id');
	}
}
