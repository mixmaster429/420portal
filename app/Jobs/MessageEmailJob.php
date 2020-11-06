<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Mail;
use App\Mail\MessageMail;

class MessageEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $to_email, $sender_name, $sender_link, $receiver_name, $receiver_link;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to_email, $sender_name, $sender_link, $receiver_name, $receiver_link)
    {
        $this->to_email = $to_email;
        $this->sender_name = $sender_name;
        $this->sender_link = $sender_link;
        $this->receiver_name = $receiver_name;
        $this->receiver_link = $receiver_link;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Mail::to($this->to_email)->send(new MessageMail($this->sender_name, $this->sender_link, $this->receiver_name, $this->receiver_link));
    }
}
