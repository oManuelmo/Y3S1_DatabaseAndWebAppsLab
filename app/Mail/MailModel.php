<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailModel extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    public function __construct($mailData) {
        $this->mailData = $mailData;
    }


  
    public function envelope()
    {
        return new Envelope(
            subject: 'Password Reset Code',
        );
    }

    public function content()
    {
        return new Content(
            view: 'pages.email',
        );
    }
}
