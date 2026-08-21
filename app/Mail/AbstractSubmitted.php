<?php

namespace App\Mail;

use App\Models\AbstractSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbstractSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $abstract;

    public function __construct(AbstractSubmission $abstract)
    {
        $this->abstract = $abstract;
    }

    public function build()
    {
        $mail = $this->subject('New Abstract Submission — ' . $this->abstract->title)
                     ->markdown('emails.abstract-submitted');

        if ($this->abstract->file_path && file_exists(public_path($this->abstract->file_path))) {
            $mail->attach(public_path($this->abstract->file_path));
        }

        return $mail;
    }
}
