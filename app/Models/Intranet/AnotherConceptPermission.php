<?php

namespace App\Models\Intranet;

use Illuminate\Database\Eloquent\Model;

class AnotherConceptPermission extends Model
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
    protected $table = 'permisos_otros_concepto';
}
