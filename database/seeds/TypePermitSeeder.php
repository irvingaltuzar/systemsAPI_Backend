<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dmirh\DmirhTypePermit;

class TypePermitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $typePermits = [
            [
                "description" => "Sin goce de sueldo",
                "with_pay" => 0,
            ],
            [
                "description" => "Con goze de sueldo",
                "with_pay" => 1,
            ],
            [
                "description" => "Otro (con goce)",
                "with_pay" => 1,
            ],
        ];

        foreach ($typePermits as $key => $type) {
            $record = new DmirhTypePermit();
            $record->description = $type['description'];
            $record->with_pay = $type['with_pay'];
            $record->save();
        }


    }
}
