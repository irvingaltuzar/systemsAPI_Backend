<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FoodMenuFile extends Model
{
	protected $table = 'dmirh_food_menu_files';

	use SoftDeletes;

	protected $guarded = [];
}
