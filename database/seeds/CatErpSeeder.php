<?php

namespace Database\Seeders;

use App\Models\CatErp;
use Illuminate\Database\Seeder;

class CatErpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$cat_erp = [
			[
				'description' => 'CONTPAQ',
			],
			[
				'description' => 'Intelisis',
			],
		];

		foreach ($cat_erp as $key => $cat_b) {
			CatErp::create($cat_b);
		}
    }
}
