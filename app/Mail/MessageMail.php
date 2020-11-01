<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sender_name, $sender_link, $receiver_name, $receiver_link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sender_name, $sender_link, $receiver_name, $receiver_link) {
        $this->sender_name = $sender_name;
        $this->sender_link = $sender_link;
        $this->receiver_name = $receiver_name;
        $this->receiver_link = $receiver_link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('420Portal - Message')->view('mail.message');
    }
}
