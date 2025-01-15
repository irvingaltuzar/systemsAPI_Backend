<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class WhatsAppMessage extends Model
{
    use SoftDeletes;

    protected $table = 'whatsapp_messages';

    public $timestamps = true;

    protected $casts = [
		'message_sent' => 'datetime:d-m-Y h:i:s',
		'message_delivered' => 'datetime:d-m-Y h:i:s',
		'message_read' => 'datetime:d-m-Y h:i:s',
		'created_at' => 'datetime:Y-m-d h:i:s',
		'updated_at' => 'datetime:Y-m-d h:i:s',
		'deleted_at' => 'datetime:Y-m-d h:i:s',

	];
}
