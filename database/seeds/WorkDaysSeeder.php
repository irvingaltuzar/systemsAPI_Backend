<?php

namespace Database\Seeders;

use App\Models\WorkDays;
use Illuminate\Database\Seeder;

class WorkDaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$work_days = [
			[ 'short_description' => 'Sun', "description" => 'Domingo'],
			[ 'short_description' => 'Mon', "description" => 'Lunes'],
			[ 'short_description' => 'Tue', "description" => 'Martes'],
			[ 'short_description' => 'Wed', "description" => 'Miércoles'],
			[ 'short_description' => 'Thu', "description" => 'Jueves'],
			[ 'short_description' => 'Fri', "description" => 'Viernes'],
			[ 'short_description' => 'Sat', "description" => 'Sábado'],
		];

		foreach ($work_days as $key => $work_day) {
			WorkDays::create($work_day);
		}
    }
}
