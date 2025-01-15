<?php

namespace App\Mail\Payroll;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Carbon\Carbon;

class notificationUnsentPayroll extends Mailable
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
        $pdf = Pdf::loadView('email.payroll.list_unsent_payroll', ['data' => $this->data, 'date_process_generated' => Carbon::now()->format('d-m-Y')]);

        return $this->markdown('email.payroll.notificationUnsentPayroll')
                    ->with('date_process_generated', Carbon::now()->format('d-m-Y'))
                    ->subject('Nómina que no se generó PDF')
                    ->attachData($pdf->output(), 'pdf_no_generados.pdf', [
                        'mime' => 'application/pdf',
                    ]);

        //return $this->markdown('email.payroll.notificationUnsentPayroll')->subject('Nómina que no se generó PDF');
    }
}
