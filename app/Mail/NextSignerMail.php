<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NextSignerMail extends Mailable
{
    use Queueable, SerializesModels;

	public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		if($this->data['sub_seccion'] == "work_permit"){
			return $this->markdown('email.work_permit.next_sign')->subject('Solicitud de permisos.');
		}elseif ($this->data['sub_seccion'] == "vacation") {
			return $this->markdown('email.vacation.next')->subject('Solicitud de vacaciones.');
		}

    }
}
