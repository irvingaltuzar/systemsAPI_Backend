<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SegUsuario;
use Faker\Generator as Faker;

$factory->define(SegUsuario::class, function (Faker $faker) {
    return [
        'nombre'=>'Maria Elena Isabel',
		'apePat'=>'Lovio',
		'apeMat'=> 'Rudolcava',
		'usuario'=>'isabel.lovio',
		'password'=> bcrypt('OupQrqJT'),
		'roles'=> '0',
		'borrado'=> '0'
    ];
});
