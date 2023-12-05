<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class DmiControlProcess
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Collection|DmiControlAuthorizationSignature[] $dmi_control_authorization_signatures
 *
 * @package App\Models
 */
class DmiControlProcess extends Model
{
	use SoftDeletes;
	protected $table = 'dmicontrol_process';

	protected $fillable = [
		'name'
	];
	protected $appends = [
		'usuario'
	];

	public function dmi_control_authorization_signatures()
	{
		return $this->hasMany(DmiControlAuthorizationSignature::class);
	}

	public function getUsuarioAttribute(){
		$usuario = DmiControlProcess::join("dmicontrol_authorization_signatures", "dmicontrol_authorization_signatures.dmi_control_process_id","dmicontrol_process.id")
							->join("personal_intelisis", "personal_intelisis.plaza_id","dmicontrol_authorization_signatures.plaza_id")
							->where("dmicontrol_process.id",$this->id)
							->where("personal_intelisis.status","ALTA")
							->select("personal_intelisis.*")
							->pluck("usuario_ad")
							->first();

		return $usuario;
	}


}
