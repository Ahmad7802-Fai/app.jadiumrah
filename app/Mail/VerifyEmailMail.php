<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class VerifyEmailMail extends Mailable implements ShouldQueue
{
    use SerializesModels;

    public $name;
    public $link;

    public function __construct($name, $link)
    {
        $this->name = $name;
        $this->link = $link;
    }

    // ===============================
    // SUBJECT
    // ===============================
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verifikasi Akun JadiUmrah'
        );
    }

    // ===============================
    // VIEW
    // ===============================
    public function content(): Content
    {
        return new Content(
            view: 'emails.verify',
            with: [
                'name' => $this->name,
                'link' => $this->link,
            ],
        );
    }
}