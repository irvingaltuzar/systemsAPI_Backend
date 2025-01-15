<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DmiControlUserSettings extends Model
{
	protected $table = 'dmicontrol_user_settings';

	use SoftDeletes;

	protected $guarded = [];
}
