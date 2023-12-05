<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatusRecruitmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('dmi_cat_status_recruitment')->insert([
            'description' => 'En proceso de Autorización',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('dmi_cat_status_recruitment')->insert([
            'description' => 'Obteniendo requisición de personal',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('dmi_cat_status_recruitment')->insert([
            'description' => 'Publicando vacantes',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('dmi_cat_status_recruitment')->insert([
            'description' => 'Concentrando CV / Solicitudes',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('dmi_cat_status_recruitment')->insert([
            'description' => 'Concertando Citas',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('dmi_cat_status_recruitment')->insert([
            'description' => 'Entrevista RH',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('dmi_cat_status_recruitment')->insert([
            'description' => 'Evaluando Psicometria',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('dmi_cat_status_recruitment')->insert([
            'description' => 'Evaluando otros filtros',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('dmi_cat_status_recruitment')->insert([
            'description' => 'Programacion entrevista con dueño de vacante',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('dmi_cat_status_recruitment')->insert([
            'description' => 'Esperando respuesta dueño de vacante',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('dmi_cat_status_recruitment')->insert([
            'description' => 'Con candidato validado',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('dmi_cat_status_recruitment')->insert([
            'description' => 'Solicitando documentos a candidato',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('dmi_cat_status_recruitment')->insert([
            'description' => 'Recabando Documentos',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('dmi_cat_status_recruitment')->insert([
            'description' => 'Agendando fecha para propuesta laboral',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('dmi_cat_status_recruitment')->insert([
            'description' => 'Propuesta Laboral Realizada',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('dmi_cat_status_recruitment')->insert([
            'description' => 'Esperando respuesta del candidato',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('dmi_cat_status_recruitment')->insert([
            'description' => 'Agendando fecha de contratación',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('dmi_cat_status_recruitment')->insert([
            'description' => 'Candidato Contratado',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('dmi_cat_status_recruitment')->insert([
            'description' => 'Se presento 1er día',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('dmi_cat_status_recruitment')->insert([
            'description' => 'Vacante Cubierta',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
