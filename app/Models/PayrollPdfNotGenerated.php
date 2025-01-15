<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PersonalIntelisis;


class PayrollPdfNotGenerated extends Model
{
    use SoftDeletes;

    protected $table = 'payroll_pdfs_not_generated';

    public $timestamps = true;

    protected $casts = [
		'created_at' => 'datetime:Y-m-d h:i:s',
		'updated_at' => 'datetime:Y-m-d h:i:s',
		'deleted_at' => 'datetime:Y-m-d h:i:s',

	];

  public function personal_intelisis(){
		return $this->belongsTo(PersonalIntelisis::class,'rfc','rfc')->where('status',"ALTA");
	}


}
