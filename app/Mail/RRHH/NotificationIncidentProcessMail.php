<?php

namespace App\Mail\RRHH;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationIncidentProcessMail extends Mailable
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
        return $this->markdown('email.incident_process.notificationEmail')
                    ->subject("Reporte de incidencias de personal - Período".$this->data['start_date'].' al '.$this->data['end_date']);
    }
}
