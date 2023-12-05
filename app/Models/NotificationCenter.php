<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationCenter extends Model
{
    protected $table = 'notification_center';

    protected $casts = [
		'created_at' => "datetime:d-m-Y h:m:s",
		'updated_at' => 'datetime:Y-m-d h:i:s',
	];
}
