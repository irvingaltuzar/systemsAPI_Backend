<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\DmiRh;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DmirhCatTypeJustification
 * 
 * @property int $id
 * @property string $description
 * @property int $deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|DmirhPersonalJustification[] $dmirh_personal_justifications
 *
 * @package  App\Models\DmiRh
 */
class DmirhCatTypeJustification extends Model
{
	protected $table = 'cat_type_justification';

	protected $casts = [
		'deleted' => 'int'
	];

	protected $fillable = [
		'description',
		'deleted'
	];

	public function dmirh_personal_justifications()
	{
		return $this->hasMany(DmirhPersonalJustification::class, 'type_id');
	}
}
