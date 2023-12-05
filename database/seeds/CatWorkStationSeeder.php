<?php

namespace Database\Seeders;

use App\Models\CatWorkStation;
use Illuminate\Database\Seeder;

class CatWorkStationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$cat_work_stations = [
			[
				'description' => 'Corporativo',
			],
			[
				'description' => 'Desarrollo',
			],
			[
				'description' => 'Externos',
			],
		];

		foreach ($cat_work_stations as $key => $cat_b) {
			CatWorkStation::create($cat_b);
		}
    }
}
