<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactSent extends Mailable
{
    use SerializesModels;

    public $enqs;
    public $subject;

    public function __construct($enqs)
    {
        $this->enqs = $enqs;
        $this->subject = 'Enquiry Sent: ' . config('app.name');
    }

    public function build()
    {
        return $this->subject($this->subject)
                    ->markdown('emails.contact');
    }
}
