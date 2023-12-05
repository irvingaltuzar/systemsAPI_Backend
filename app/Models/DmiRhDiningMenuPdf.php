<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class DmiRhDiningMenuPdf extends Model
{
	use SoftDeletes;

	protected $table = 'dmirh_dining_menu_pdfs';

	protected $guarded = [];

}
