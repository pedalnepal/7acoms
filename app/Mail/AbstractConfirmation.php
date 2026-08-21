<?php

namespace App\Mail;

use App\Models\AbstractSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbstractConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $abstract;

    public function __construct(AbstractSubmission $abstract)
    {
        $this->abstract = $abstract;
    }

    public function build()
    {
        return $this->subject('We have received your abstract — ' . config('app.name'))
                    ->markdown('emails.abstract-confirmation');
    }
}
