<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgetPass extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $user;

    public function __construct($code, $user)
    {
        $this->code = $code;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.forgetPassword')->with([
            'token' => $this->code,
            'user'  => $this->user
        ]);
    }
}