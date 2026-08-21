<?php

namespace App\Mail;

use App\Models\PaymentTransaction;
use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationPaymentReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $registration;

    public $transaction;

    /** The committee's copy carries a different subject line. */
    public $forAdmin;

    public function __construct(Registration $registration, PaymentTransaction $transaction, bool $forAdmin = false)
    {
        $this->registration = $registration;
        $this->transaction  = $transaction;
        $this->forAdmin     = $forAdmin;
    }

    public function build()
    {
        $subject = $this->forAdmin
            ? 'Registration payment received — ' . $this->registration->full_name
            : 'Payment received for your registration — ' . config('app.name');

        return $this->subject($subject)
                    ->markdown('emails.registration-payment-receipt');
    }
}
