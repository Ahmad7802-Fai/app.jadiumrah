<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmailMail extends Mailable
{
    use SerializesModels;

    public $name;
    public $link;

    public function __construct($name, $link)
    {
        $this->name = $name;
        $this->link = $link;
    }

    public function build()
    {
        return $this->subject('Verifikasi Akun JadiUmrah')
            ->view('emails.verify')
            ->with([
                'name' => $this->name,
                'link' => $this->link,
            ]);
    }
}