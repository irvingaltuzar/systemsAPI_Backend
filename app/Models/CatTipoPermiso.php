<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CatTipoPermiso extends Model
{
	protected $table = 'cat_tipo_permiso';
	protected $primaryKey = 'tipoPermiso_Id';
	public $timestamps = false;

    protected $casts = [
		'borrado' => 'float'
	];

    protected $fillable = [
		'tipoPermiso_Val',
        'tipoPermiso_Desc',
		'borrado'
	];
}
