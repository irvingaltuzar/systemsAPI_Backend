<?php

namespace App\Models\Intranet;

use Illuminate\Database\Eloquent\Model;

class DmiFirma extends Model
{
    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql2';

    /**
    * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'dmi_firmas';
    public $timestamps = false;
}