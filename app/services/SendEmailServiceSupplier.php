<?php

namespace App\Services;

use App\Models\DmiabaSupplierRegistration;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SendEmailServiceSupplier
{
	public function EFO($data,$mails)
	{
		// $mails = [
		// 	'irving.altuzar@grupodmi.com.mx'
		// ];

		// $data = DmiabaSupplierRegistration::first();


		dispatch(new \App\Jobs\SendEmailJob($mails, $data, "efo"))->afterResponse();
	}

	public function newSupplierNotification($data,$mails)
	{

		dispatch(new \App\Jobs\SendEmailJob($mails, $data, "new_Supplier"))->afterResponse();
	}

	public function EditSupplierNotification($data,$mails)
	{

		dispatch(new \App\Jobs\SendEmailJob($mails, $data, "edit_Supplier"))->afterResponse();
	}
}
