<?php

namespace App\Mail;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $registration;

    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
    }

    public function build()
    {
        $mail = $this->subject('New Registration — ' . $this->registration->full_name)
                     ->markdown('emails.registration-submitted');

        foreach ([$this->registration->id_card_path, $this->registration->receipt_path] as $path) {
            if ($path && file_exists(public_path($path))) {
                $mail->attach(public_path($path));
            }
        }

        return $mail;
    }
}
