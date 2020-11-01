<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PortalRegisterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $portal;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($portal) {
        $this->portal = $portal;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('420Portal - ' . $this->portal->name)->view('mail.portal_register');
    }
}
