<?php

use Illuminate\Database\Seeder;
use App\Models\SegUsuario;
class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(SegUsuario::class)->times(1)->create();
      
    }
}
