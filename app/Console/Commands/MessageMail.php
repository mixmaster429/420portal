<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\UserChat;
use App\User;

use App\Jobs\MessageEmailJob;

use Mail;
use App\Mail\MessageMail as MessageMailer;

class MessageMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send message mail to companies and users for every 10mins';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $receivers = Userchat::whereRead(0)->where('email_sent', '!=', 1)->distinct()->pluck('user_to')->toArray();
        for ($i=0; $i < count($receivers); $i++) { 
            $receiver = User::find($receivers[$i]);
            if($receiver){
                $to_email = $receiver->email;
                if(filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
                    $senders = Userchat::where('user_to', $receiver->id)->distinct()->pluck('user_id')->toArray();
                    for ($j=0; $j < count($senders); $j++) { 
                        $sender = User::find($senders[$j]);
                        if($sender) {
                            // dispatch(new MessageEmailJob($to_email, $sender->name, $sender->name, $receiver->name, $receiver->name));
                            Mail::to($to_email)->send(new MessageMailer($sender->name, $sender->name, $receiver->name, $receiver->name));
                        }
                    }
                }
            }
        }
        Userchat::whereRead(0)->where('email_sent', '!=', 1)->update(['email_sent' => 1]);

        $receivers = Companychat::whereRead(0)->where('email_sent', '!=', 1)->distinct()->pluck('receiver')->toArray();
        for ($i=0; $i < count($receivers); $i++) { 
            $receiver = Portal::find($receivers[$i]);
            if($receiver){
                $to_email = $receiver->email;
                if(filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
                    $senders = Companychat::where('receiver', $receiver->id)->distinct()->pluck('sender')->toArray();
                    for ($j=0; $j < count($senders); $j++) { 
                        $sender = User::find($senders[$j]);
                        if($sender) {                           
                            // dispatch(new MessageEmailJob($to_email, $sender->name, $sender->name, $receiver->name, $receiver->username));
                            Mail::to($to_email)->send(new MessageMailer($sender->name, $sender->name, $receiver->name, $receiver->username)); 
                        }
                    }
                }
            }
        }
        Companychat::whereRead(0)->where('email_sent', '!=', 1)->update(['email_sent' => 1]);
    }
}
