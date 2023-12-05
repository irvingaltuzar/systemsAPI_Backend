<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DocumentoMetadato
 * 
 * @property int $documentoMetadatoId
 * @property int $documentodId
 * @property string $descripcion
 * @property float $borrado
 * 
 * @property Documentod $documentod
 *
 * @package App\Models
 */
class DocumentoMetadato extends Model
{
	protected $table = 'documento_metadato';
	protected $primaryKey = 'documentoMetadatoId';
	public $timestamps = false;

	protected $casts = [
		'documentodId' => 'int',
		'borrado' => 'float'
	];

	protected $fillable = [
		'documentodId',
		'descripcion',
		'borrado'
	];

	public function documentod()
	{
		return $this->belongsTo(Documentod::class, 'documentodId');
	}
}
