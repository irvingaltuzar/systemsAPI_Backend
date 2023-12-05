<?php

namespace Database\Seeders;

use App\Models\CatFoodType;
use Illuminate\Database\Seeder;

class CatFoodTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$cat_food_type = [
			[
				'description' => 'Estándar',
			],
			[
				'description' => 'Vegetariano',
			],
			[
				'description' => 'Vegano',
			],
			[
				'description' => 'Light',
			],
		];

		foreach ($cat_food_type as $key => $cat_b) {
			$cat_food_type = CatFoodType::create($cat_b);
		}
    }
}
