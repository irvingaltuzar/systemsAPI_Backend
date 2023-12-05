<?php

namespace App\Mail\RRHH;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkPermitMail extends Mailable
{
    use Queueable, SerializesModels;

	public $data,$type;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data,$_type)
    {
        $this->data = $data;
        $this->type = $_type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		if($this->type == "work_permit_notification"){
			return $this->markdown('email.work_permit.notificationEmailCoordRRHH')->subject('Permiso autorizado');
		}else{
			return $this->markdown('email.work_permit.notificationEmail')->subject('Solicitud de Permiso.');
		}

    }
}
