<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dmirh\DmirhPermitConcept;


class PermitConceptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $typePermits = [
            [
                "dmirh_type_permits_id" => 2,
                "description" => "Paternidad",
                "days" => 5,
            ],
            [
                "dmirh_type_permits_id" => 2,
                "description" => "Fallecimiento de familiar directio (en la misma localidad)",
                "days" => 3,
            ],
            [
                "dmirh_type_permits_id" => 2,
                "description" => "Fallecimiento de familiar directo (fuera de su localidad)",
                "days" => 4,
            ],
            [
                "dmirh_type_permits_id" => 2,
                "description" => "Primeras nupcias",
                "days" => 2,
            ],
            [
                "dmirh_type_permits_id" => 2,
                "description" => "Graduación",
                "days" => 1,
            ],
            [
                "dmirh_type_permits_id" => 3,
                "description" => "Curso",
                "days" => null,
            ],
            [
                "dmirh_type_permits_id" => 3,
                "description" => "Compensación de tiempo",
                "days" => null,
            ],
            [
                "dmirh_type_permits_id" => 3,
                "description" => "Enfermedad",
                "days" => null,
            ],
            [
                "dmirh_type_permits_id" => 3,
                "description" => "Asunto personal",
                "days" => null,
            ],

        ];

        foreach ($typePermits as $key => $type) {
            $record = new DmirhPermitConcept();
            $record->dmirh_type_permits_id = $type['dmirh_type_permits_id'];
            $record->description = $type['description'];
            $record->days = $type['days'];
            $record->save();
        }

    }
}
