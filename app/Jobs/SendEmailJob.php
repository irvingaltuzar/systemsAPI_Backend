<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Bus\Queueable;
use App\Mail\DiningMail;
use App\Mail\newRequisitionMail;
use App\Mail\SupplierEFOMail;
use App\Mail\newSupplierMail;
use App\Mail\NextSignerMail;
use App\Mail\SupplierEditMail;
use App\Mail\RRHH\WorkPermitMail;
use App\Mail\RRHH\WorkPermitNotificationRRHHMail;
use App\Mail\RRHH\VacationMail;
use App\Mail\SupplierApproved;
use App\Mail\SupplierCanceled;
use App\Mail\SupplierRemoved;
use App\Mail\signRequisitionMail;
use App\Mail\validateRequisitionMail;
use App\Mail\cancelRequisitionMail;
use App\Mail\autorizeRequisitionMail;

class SendEmailJob implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $email_list, $data, $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email_list, $data, $type)
    {
        $this->email_list = $email_list;
		$this->data = $data;
		$this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
		if ($this->type === "Food") {
			Mail::to($this->email_list)
				->send(new DiningMail($this->data));

		}else if($this->type === "work_permit" || $this->type === "work_permit_notification"){
			Mail::to($this->email_list)
				->send(new WorkPermitMail($this->data,$this->type));
		}else if($this->type === "vacation"){
			Mail::to($this->email_list)
				->send(new VacationMail($this->data));
		}

		if ($this->type === "efo") {
			Mail::to($this->email_list)
				->send(new SupplierEFOMail($this->data));
		}
		if ($this->type === "new_Supplier") {
			Mail::to($this->email_list)
				->send(new newSupplierMail($this->data));
		}

		if ($this->type === "edit_Supplier") {
			Mail::to($this->email_list)
				->send(new SupplierEditMail($this->data));
		}
		if ($this->type == "cancel") {
			if ($this->email_list['second_mails'][0] != null) {
				Mail::to($this->email_list['main_mails'])
					->cc($this->email_list['second_mails'])
					->send(new SupplierCanceled($this->data));
			} else {
				Mail::to($this->email_list['main_mails'])
					->send(new SupplierCanceled($this->data));
			}
		}

		if ($this->type == "approve") {
				Mail::to($this->email_list['main_mails'])
					->send(new SupplierApproved($this->data));
		}

		if ($this->type == "remove") {
			Mail::to($this->email_list['main_mails'])
				->send(new SupplierRemoved($this->data));
		}

		if ($this->type == "next_signer") {
			Mail::to($this->email_list)
				->send(new NextSignerMail($this->data));
		}

		if ($this->type == "new_requisition") {
			Mail::to($this->email_list)
				->send(new newRequisitionMail($this->data));
		}
		if ($this->type == "sign_requisition") {
			Mail::to($this->email_list)
				->send(new signRequisitionMail($this->data));
		}

		if ($this->type == "validate_requisition") {
			Mail::to($this->email_list)
				->send(new validateRequisitionMail($this->data));
		}

		if ($this->type == "cancel_requisition") {
			Mail::to($this->email_list)
				->send(new cancelRequisitionMail($this->data));
		}
		if ($this->type == "autorize_requisition") {
			Mail::to($this->email_list)
				->send(new autorizeRequisitionMail($this->data));
		}
    }
}
