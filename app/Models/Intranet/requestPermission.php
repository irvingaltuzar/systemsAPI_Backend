<?php

namespace App\Models\Intranet;

use Illuminate\Database\Eloquent\Model;

class requestPermission extends Model
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
    protected $table = 'solicitudes_permisos';
    public $timestamps = false;
    protected $primaryKey = 'idsolicitud';

    public function solicitudes()
    {
        return $this->hasMany('App\Models\Intranet\DmiSolicitud', 'llave', 'firma');
    }

    public function firmas()
    {
        return $this->hasMany('App\Models\Intranet\DmiFirma', 'llave', 'firma');
    }
}
