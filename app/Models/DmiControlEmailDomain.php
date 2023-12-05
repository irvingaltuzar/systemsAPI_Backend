<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class DmiControlEmailDomain
 * 
 * @property int $id
 * @property string $domain
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models
 */
class DmiControlEmailDomain extends Model
{
	use SoftDeletes;
	protected $table = 'dmicontrol_email_domain';

	protected $fillable = [
		'domain'
	];
}
