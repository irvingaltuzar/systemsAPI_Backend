<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DmiRhFoodOfferProduct extends Model
{
	use SoftDeletes;

	protected $guarded = [];

	protected $table = 'dmirh_food_offer_products';

	public function product()
	{
		return $this->belongsTo(DmiRhFoodOrderProduct::class, 'food_order_product_id');
	}
}
