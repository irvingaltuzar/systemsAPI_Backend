<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PersonalIntelisis as PersonalIntelisis;
use App\Models\DmiRh\DmirhPersonalTime;
use App\Models\DmiRh\DmirhPersonalTimeDetail;
use App\Models\SegUsuario as Usuario;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use App\Repositories\IntelisisRepository;
use App\Http\Controllers\Alfa\RecursosHumanos\PersonalIntelisisController;
class UpdatePersonalIntelisis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:personalIntelisis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualización informacion Personal Intelisis';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

      $PersonalIntelisisController = new PersonalIntelisisController();
      $update_PersonalIntelisis_SP = $PersonalIntelisisController->updatePersonalIntelisisSP();
      
      
    }
}
