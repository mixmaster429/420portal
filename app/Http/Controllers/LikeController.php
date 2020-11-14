<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Notification;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mail;
use App\Mail\NotificationMail;


class LikeController extends Controller
{
    public function create(Request $request) {
        $target_id = $request->target_id;
        $target_model = $request->target_model;
        $user_id = auth()->id();
        $liked = Like::where('target_id', $target_id)->where('model', $target_model)->where('user_id', $user_id)->count();

        if (0 == $liked) {
            $like = new Like();
            $like['user_id'] = $user_id;
            $like['target_id'] = $request->target_id;
            $like['model'] = $request->target_model;

            $like->save();
            if($request->target_model == 'post' && $user_id != $like->target->user_id) {
                if($like->target->portal_id) {
                    $notification_type = 'portal';                        
                } else {
                    $notification_type = 'user';
                }
                $notification = Notification::create([
                    'type' => 'like',
                    'notifier_id' => $user_id,
                    'user_id' => $like->target->user_id ?? '',
                    'media_type' => $notification_type,
                    'notifiable_id' => $request->target_id,
                    'notifiable_type' => 'App\Models\Media',
                ]);
                if($notification->user && $notification->user->check_notification_filter('like')){
                    $toEmail = $notification->user->email;
                    if($notification_type == 'portal') {
                        $toEmail = $like->target->portal->email ?? '';                        
                    }
                    if(filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
                        try {
                            Mail::to($toEmail)->send(new NotificationMail($notification)); 
                        } catch (\Throwable $th) {
                            //throw $th;
                        }
                    }
                }
            }
        } else {
            Like::where('target_id', $target_id)->where('model', $target_model)->where('user_id', $user_id)->delete();
        }
        $likes = Like::where('target_id', $target_id)->where('model', $request->get('target_model'))->count();
        return response()->json($likes);
    }

    public function delete(Request $request)
    {
        $target_id = $request->target_id;
        $user_id = auth()->id();
        $liked = Like::where('target_id', $target_id)->where('user_id', $user_id)->where('model', $request->get('target_model'))->count();

        if ($liked) {
            $affectedRows = Like::where('target_id', $target_id)->where('user_id', $user_id)->where('model', $request->get('target_model'))->delete();
        }
        $likes = Like::where('target_id', $target_id)->where('model', $request->get('target_model'))->count();

        return response()->json($likes);
    }

    public function addcommentlike(Request $request)
    {
        $comment_id = $request->target_id;

        if ($auth_id = auth()->id()) {
            $like = new Like();
            $like->user_id = $auth_id;
            $like->target_id = $comment_id;
            $like->model = 'comment';

            $like->save();
            if($like->target->target_model == 'media' && $auth_id != $like->target->user_id){
                if($like->target->portal_id) {
                    $notification_type = 'portal';                        
                } else {
                    $notification_type = 'user';
                }
                $notification = Notification::create([
                    'type' => 'like_comment',
                    'notifier_id' => $auth_id,
                    'user_id' => $like->target->user_id ?? '',
                    'media_type' => $notification_type,
                    'notifiable_id' => $like->target->target_id,
                    'notifiable_type' => 'App\Models\Media',
                ]);
                if($notification->user && $notification->user->check_notification_filter('like')){
                    $toEmail = $notification->user->email;
                    if($notification_type == 'portal') {
                        $toEmail = $like->target->portal->email ?? '';
                    }
                    try {
                        Mail::to($toEmail)->send(new NotificationMail($notification));
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
            }

            
            if($like->target->target_model == 'news' && $auth_id != $like->target->user_id){
                $notification = Notification::create([
                    'type' => 'like_news_comment',
                    'notifier_id' => $auth_id,
                    'user_id' => $like->target->user_id ?? '',
                    'notifiable_id' => $like->target->target_id,
                    'notifiable_type' => 'App\Post',
                ]);
                if($notification->user && $notification->user->check_notification_filter('like')){
                    $toEmail = $notification->user->email;
                    try {
                        Mail::to($toEmail)->send(new NotificationMail($notification));
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
            }

            $return = [];
            $return['userliked'] = true;
            $return['likes'] = Like::where('target_id', $comment_id)->where('model', 'comment')->count();

            return response()->json($return);
        }
        $return = [];
        $return['userliked'] = false;
        $return['likes'] = Like::where('target_id', $comment_id)->where('model', 'comment')->count();

        return response()->json($return);
    }

    public function deletecommentlike(Request $request)
    {
        $comment_id = $request->target_id;

        if ($user_id = auth()->id()) {
            Like::where('user_id', $user_id)->where('target_id', $comment_id)->where('model', 'comment')->delete();

            $return = [];
            $return['userliked'] = false;
            $return['likes'] = Like::where('target_id', $comment_id)->where('model', 'comment')->count();

            return response()->json($return);
        }
        $return = [];
        $return['userliked'] = true;
        $return['likes'] = Like::where('target_id', $comment_id)->where('model', 'comment')->count();

        return response()->json($return);
    }

    public function likeProfile(Request $request)
    {
        $profile = $request->target_id;

        if (Auth::check()) {
            $like = new Like();
            $like->user_id = Auth::id();
            $like->target_id = $profile;
            $like->model = 'portal';

            $like->save();

            $return = [];
            $return['userliked'] = true;
            $return['likes'] = Like::where('target_id', $profile)->where('model', 'profile')->count();

            return response()->json($return);
        }
        $return = [];
        $return['userliked'] = false;
        $return['likes'] = Like::where('target_id', $profile)->where('model', 'portal')->count();

        return response()->json($return);
    }

    public function unlikeProfile(Request $request)
    {
        $profile = $request->target_id;

        if (Auth::check()) {
            $user_id = Auth::id();
            Like::where('user_id', $user_id)->where('target_id', $profile)->where('model', 'portal')->delete();

            $return = [];
            $return['userliked'] = false;
            $return['likes'] = Like::where('target_id', $profile)->where('model', 'portal')->count();

            return response()->json($return);
        }

        $return = [];
        $return['userliked'] = false;
        $return['likes'] = Like::where('target_id', $profile)->where('model', 'portal')->count();

        return response()->json($return);
    }

}
