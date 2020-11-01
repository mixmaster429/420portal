<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StrainRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user, $menu;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $menu)
    {
        $this->user = $user;
        $this->menu = $menu;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('420Portal - Strain Request')->view('mail.strain_request');
    }
}
