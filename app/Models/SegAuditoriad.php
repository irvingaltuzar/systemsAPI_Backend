<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SegAuditoriad
 * 
 * @property int $auditoriaDId
 * @property int $auditoriaId
 * @property string $comentarios
 * 
 * @property SegAuditorium $seg_auditorium
 *
 * @package App\Models
 */
class SegAuditoriad extends Model
{
	protected $table = 'seg_auditoriad';
	protected $primaryKey = 'auditoriaDId';
	public $timestamps = false;

	protected $casts = [
		'auditoriaId' => 'int'
	];

	protected $fillable = [
		'auditoriaId',
		'comentarios'
	];

	public function seg_auditorium()
	{
		return $this->belongsTo(SegAuditorium::class, 'auditoriaId');
	}
}
