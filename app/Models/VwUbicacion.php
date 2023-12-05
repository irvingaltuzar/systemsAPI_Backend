<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VwUbicacion
 * 
 * @property string|null $ubicacion
 *
 * @package App\Models
 */
class VwUbicacion extends Model
{
	protected $table = 'vw_ubicacion';
	public $incrementing = false;
	public $timestamps = false;

	protected $fillable = [
		'ubicacion'
	];
}
