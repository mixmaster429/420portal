<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $mdata, $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mdata, $user)
    {
        $this->mdata = $mdata;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Register - ' . $this->user->name)->view('mail.sendmail');
    }
}
