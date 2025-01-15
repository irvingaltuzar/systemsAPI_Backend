<?php

namespace App\Mail\RRHH;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkScheduleMail extends Mailable
{
    use Queueable, SerializesModels;

	public $data,$type;


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
		if($this->data['type'] == "collaborator"){
			return $this->markdown('email.work_schedule.notificationEmail')->subject('Solicitud de Cambio de Horario');

		}elseif($this->data['type'] == "boss"){
			return $this->markdown('email.work_schedule.notificationEmailBoss')->subject('Solicitud de Cambio de Horario');

		}elseif($this->data['type'] == "rrhh"){
			return $this->markdown('email.work_schedule.notificationEmailRRHH')->subject('Solicitud de Cambio de Horario');
		}

    }
}
