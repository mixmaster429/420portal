<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FlagMail extends Mailable
{
    use Queueable, SerializesModels;

    public $flag;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($flag)
    {
        $this->flag = $flag;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('420Portal - Flag')->view('mail.flag');
    }
}
