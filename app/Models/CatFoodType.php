<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class CatFoodType extends Model
{
	use SoftDeletes;

	protected $table = 'cat_food_type';


	protected $guarded = [];
}
