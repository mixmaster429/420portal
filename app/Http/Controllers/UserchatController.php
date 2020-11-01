<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Models\UserChat;

class UserchatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function fetchMessages(Request $request)
    {
        $sender = $request->get('sender');
        $receiver = $request->get('receiver');
        // sender = user_id
        // receiver = user_with
        $messages = Userchat::with('user')->whereIn('user_id', [$receiver, $sender])->whereIn('user_to', [$receiver, $sender])->where('deleted_by', '<>', $sender)->get();
        $first_unread_flag = 0;
        foreach ($messages as $message) {
            if($message->read == 0 && $first_unread_flag == 0){
                $message->first_unread = 1;
                $first_unread_flag = 1;
            }else{
                $message->first_unread = 0;
            }
            if ($message->user_id == $sender) {
                $message->type = 1;
            } else {
                $message->type = 0;
            }
        }

        return $messages;
    }

    public function sendMessage(Request $request)
    {
        $user = auth()->user();

        $created_at = $user->userchat()->create([
            'user_to' => $request->input('receiver'),
            'message' => $request->input('message')
        ])->created_at;

        return ['status' => true, 'created_at' => $created_at];
    }

    public function imageupload(Request $request, $sender, $receiver)
    {

        $imageables = ['jpg', 'JPG', 'jpeg', 'png', 'gif'];
        $attachment = request()->file('file');

        $imageName = time() . '.' . $attachment->getClientOriginalExtension();
        $attachment->move(public_path('uploaded/chat'), $imageName);

        $user = User::find($sender);

        $fileurl = '/uploaded/chat/' . $imageName;

        $created_at = $user->userchat()->create([
            'user_to' => $receiver,
            'message' => $fileurl,
            'file' => 1
        ])->created_at;

        return ['status' => true, 'created_at' => $created_at, 'url' => $fileurl];
    }

    public function readall(Request $request)
    {
        $loggeduser = auth()->id();
        $receiver = $request->get('receiver');
        $userchat = Userchat::where('user_id', $receiver)->where('user_to', $loggeduser)->get();
        foreach ($userchat as $chat){
            $chat->read = 1;
            $chat->save();
        }

        return ['status' => true];
    }
}
