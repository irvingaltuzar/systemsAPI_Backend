<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Models\SegUsuario::class, function (Faker $faker) {
    return [
        'ruta'=> 'IT/DMI',
        'nombre' => $faker->firstName,
        'apellidos'=>$faker->LastName,
        'email' => $faker->unique()->safeEmail,
        'roles' => 'admin',
        'usuario'=> $faker->userName,
        // 'email_verified_at' => now(),
        'password' => bcrypt('OupQrqJT') , // password
        // 'remember_token' => Str::random(10),
    ];
});
