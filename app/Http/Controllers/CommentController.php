<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Notification;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mail;
use App\Mail\NotificationMail;



class CommentController extends Controller
{
    public function destroy(Request $request)
    {
        $user_id = auth()->id();
        $comment_id = $request->get('comment_id');
        $target_id = $request->get('target_id');
        $target_model = $request->get('target_model');

        Comment::find($comment_id)->delete();

        Comment::where('parent_id', $comment_id)->delete();

        $comments = Comment::with('user')->where('target_id', $target_id)->where('target_model', $target_model)->where('parent_id', 0)->orderBy('created_at', 'desc')->get();
        foreach ($comments as $item) {
            $item->user->media;
            $item->user->profilePic;

            if ($item->user_id == $user_id || $user_id == 1) {
                $item->owned = true;
            } else {
                $item->owned = false;
            }

            $comment_id = $item->id;
            $userliked = Like::where('user_id', $user_id)->where('target_id', $comment_id)->where('model', 'comment')->count();
            if ($userliked > 0) {
                $item->userliked = true;
            } else {
                $item->userliked = false;
            }

            $likes = Like::where('model', 'comment')->where('target_id', $comment_id)->count();
            $item->likes = $likes;

            $sub_comments = Comment::with('user')->where('target_id', $target_id)->where('parent_id', $comment_id)->orderBy('created_at', 'desc')->get();

            foreach ($sub_comments as $sub_item) {
                $sub_item->user->media;
                $sub_item->user->profilePic;

                if ($sub_item->user_id == $user_id || $user_id == 1) {
                    $sub_item->owned = true;
                } else {
                    $sub_item->owned = false;
                }

                $comment_id = $sub_item->id;
                $userliked = Like::where('user_id', $user_id)->where('target_id', $comment_id)->where('model', 'comment')->count();
                if ($userliked > 0) {
                    $sub_item->userliked = true;
                } else {
                    $sub_item->userliked = false;
                }

                $likes = Like::where('model', 'comment')->where('target_id', $comment_id)->count();
                $sub_item->likes = $likes;
                // Sub2 Comments
                $sub2_comments = Comment::orderBy('created_at', 'desc')->with('user')->where('target_id', $target_id)->where('parent_id', $comment_id)->get();
                foreach ($sub2_comments as $sub2_item) {
                    $sub2_item->user->media;
                    $sub2_item->user->profilePic;

                    if ($sub2_item->user_id == $user_id || $user_id == 1) {
                        $sub2_item->owned = true;
                    } else {
                        $sub2_item->owned = false;
                    }

                    $comment_id = $sub2_item->id;
                    $userliked = Like::where('user_id', $user_id)->where('target_id', $comment_id)->where('model', 'comment')->count();
                    if ($userliked > 0) {
                        $sub2_item->userliked = true;
                    } else {
                        $sub2_item->userliked = false;
                    }

                    $likes = Like::where('model', 'comment')->where('target_id', $comment_id)->count();
                    $sub2_item->likes = $likes;
                }
                $sub_item->sub2_comments = $sub2_comments;
            }

            $item->sub_comment = $sub_comments;
        }

        return response()->json($comments);
    }

    public function addcomment(Request $request)
    {
        $user_id = Auth::id();
        $target_id = $request->target_id;

        $reply_to = $request->reply;

        if (null == $reply_to) {
            $comment = new Comment();
            $comment->text = $request->text;
            $comment->target_id = $request->target_id;
            $comment->target_model = $request->target_model;
            $comment->user_id = $user_id;
            $comment->save();
            if($comment->target) {
                $comment->target->update([
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            if($request->target_model == 'media' && Auth::id() != $comment->target->user_id) {
                if($comment->target->portal_id) {
                    $notification_type = 'portal';                        
                } else {
                    $notification_type = 'user';
                }
                $notification = Notification::create([
                    'type' => 'comment',
                    'notifier_id' => $user_id,
                    'user_id' => $comment->target->user_id ?? '',
                    'notifiable_id' => $request->target_id,
                    'media_type' => $notification_type,
                    'notifiable_type' => 'App\Media',
                ]);
                if($notification->user && $notification->user->check_notification_filter('comment_reply')){
                    $toEmail = $notification->user->email;
                    if($notification_type == 'portal') {
                        $toEmail = $comment->target->portal->email ?? '';                        
                    }
                    if(filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
                        Mail::to($toEmail)->send(new NotificationMail($notification)); 
                    }                     
                }
            }

        } else {
            $comment = new Comment();
            $parent = Comment::find($reply_to);
            $reply_name = $parent->user->type == 'user' ? $parent->user->username : $parent->user->name;
            $parent_name = '@'.$reply_name;
            if(substr($request->text, 0, 1) == '@' && strpos($request->text, $parent_name) !== false) {
                $sub_comment = str_replace($parent_name, '', $request->text);
            } else {
                $sub_comment = $request->text;
            }
            $comment->text = $sub_comment;
            $comment->target_id = $request->target_id;
            $comment->target_model = $request->target_model;
            $comment->parent_id = $reply_to;
            $comment->user_id = $user_id;

            $comment->save();
            if($comment->target) {
                $comment->target->update([
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            if($request->target_model == 'media' && Auth::id() != $comment->target->user_id) {
                if($comment->target->portal_id) {
                    $notification_type = 'portal';                        
                } else {
                    $notification_type = 'user';
                }
                $notification = Notification::create([
                    'type' => 'reply',
                    'notifier_id' => $user_id,
                    'user_id' => $comment->target->user_id ?? '',
                    'media_type' => $notification_type,
                    'notifiable_id' => $request->target_id,
                    'notifiable_type' => 'App\Media',
                ]);
                if($notification->user && $notification->user->check_notification_filter('comment_reply')){
                    $toEmail = $notification->user->email;
                    if($notification_type == 'portal') {
                        $toEmail = $comment->target->portal->email ?? '';                        
                    }
                    if(filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
                        Mail::to($toEmail)->send(new NotificationMail($notification)); 
                    }
                }
            }
            if($request->target_model == 'news' && Auth::id() != $comment->parent->user_id) {
                $notification = Notification::create([
                    'type' => 'reply_news',
                    'notifier_id' => $user_id,
                    'user_id' => $comment->parent->user_id,
                    'notifiable_id' => $request->target_id,
                    'notifiable_type' => 'App\Post',
                ]);
                if($notification->user->check_notification_filter('comment_reply')){
                    $toEmail = $notification->user->email;
                    if(filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
                        Mail::to($toEmail)->send(new NotificationMail($notification)); 
                    }
                }
            }
        }

        $comments = Comment::orderBy('created_at', 'desc')->with('user')->where('target_id', $target_id)->where('target_model', $request->target_model)->where('parent_id', 0)->get();
        foreach ($comments as $item) {
            $item->user->profilePic;

            if ($item->user_id == $user_id || $user_id == 1) {
                $item->owned = true;
            } else {
                $item->owned = false;
            }

            $comment_id = $item->id;
            $userliked = Like::where('user_id', $user_id)->where('target_id', $comment_id)->where('model', 'comment')->count();
            if ($userliked > 0) {
                $item->userliked = true;
            } else {
                $item->userliked = false;
            }

            $likes = Like::where('model', 'comment')->where('target_id', $comment_id)->count();
            $item->likes = $likes;

            $sub_comments = Comment::orderBy('created_at', 'desc')->with('user')->where('target_id', $target_id)->where('parent_id', $comment_id)->get();

            foreach ($sub_comments as $sub_item) {
                $sub_item->user->profilePic;

                if ($sub_item->user_id == $user_id || $user_id == 1) {
                    $sub_item->owned = true;
                } else {
                    $sub_item->owned = false;
                }

                $comment_id = $sub_item->id;
                $userliked = Like::where('user_id', $user_id)->where('target_id', $comment_id)->where('model', 'comment')->count();
                if ($userliked > 0) {
                    $sub_item->userliked = true;
                } else {
                    $sub_item->userliked = false;
                }

                $likes = Like::where('model', 'comment')->where('target_id', $comment_id)->count();
                $sub_item->likes = $likes;

                // Sub2 Comments
                $sub2_comments = Comment::orderBy('created_at', 'desc')->with('user')->where('target_id', $target_id)->where('parent_id', $comment_id)->get();
                foreach ($sub2_comments as $sub2_item) {
                    $sub2_item->user->profilePic;

                    if ($sub2_item->user_id == $user_id || $user_id == 1) {
                        $sub2_item->owned = true;
                    } else {
                        $sub2_item->owned = false;
                    }

                    $comment_id = $sub2_item->id;
                    $userliked = Like::where('user_id', $user_id)->where('target_id', $comment_id)->where('model', 'comment')->count();
                    if ($userliked > 0) {
                        $sub2_item->userliked = true;
                    } else {
                        $sub2_item->userliked = false;
                    }

                    $likes = Like::where('model', 'comment')->where('target_id', $comment_id)->count();
                    $sub2_item->likes = $likes;
                }
                $sub_item->sub2_comments = $sub2_comments;
            }

            $item->sub_comment = $sub_comments;
        }
        return response()->json($comments);
    }

    public function getcomment(Request $request)
    {
        $user_id = auth()->user()->id ?? '';
        $target_id = $request->target_id;
        $target_model = $request->target_model;
        $comments = Comment::with('user')
                    ->where('target_id', $target_id)
                    ->where('target_model', $target_model)
                    ->where('parent_id', 0)
                    ->orderBy('created_at', 'desc')->get();
        foreach ($comments as $item) {
            $item->user->profilePic;

            if ($item->user_id == $user_id || $user_id == 1) {
                $item->owned = true;
            } else {
                $item->owned = false;
            }

            $comment_id = $item->id;
            $userliked = Like::where('user_id', $user_id)->where('target_id', $comment_id)->where('model', 'comment')->count();
            if ($userliked > 0) {
                $item->userliked = true;
            } else {
                $item->userliked = false;
            }

            $likes = Like::where('model', 'comment')->where('target_id', $comment_id)->count();
            $item->likes = $likes;

            $sub_comments = Comment::orderBy('created_at', 'desc')->with('user')->where('target_id', $target_id)->where('parent_id', $comment_id)->get();

            foreach ($sub_comments as $sub_item) {
                $sub_item->user->profilePic;

                if ($sub_item->user_id == $user_id || $user_id == 1) {
                    $sub_item->owned = true;
                } else {
                    $sub_item->owned = false;
                }

                $comment_id = $sub_item->id;
                $userliked = Like::where('user_id', $user_id)->where('target_id', $comment_id)->where('model', 'comment')->count();
                if ($userliked > 0) {
                    $sub_item->userliked = true;
                } else {
                    $sub_item->userliked = false;
                }

                $likes = Like::where('model', 'comment')->where('target_id', $comment_id)->count();
                $sub_item->likes = $likes;
                // Sub2 Comments
                $sub2_comments = Comment::orderBy('created_at', 'desc')->with('user')->where('target_id', $target_id)->where('parent_id', $comment_id)->get();
                foreach ($sub2_comments as $sub2_item) {
                    $sub2_item->user->profilePic;

                    if ($sub2_item->user_id == $user_id || $user_id == 1) {
                        $sub2_item->owned = true;
                    } else {
                        $sub2_item->owned = false;
                    }

                    $comment_id = $sub2_item->id;
                    $userliked = Like::where('user_id', $user_id)->where('target_id', $comment_id)->where('model', 'comment')->count();
                    if ($userliked > 0) {
                        $sub2_item->userliked = true;
                    } else {
                        $sub2_item->userliked = false;
                    }

                    $likes = Like::where('model', 'comment')->where('target_id', $comment_id)->count();
                    $sub2_item->likes = $likes;
                }
                $sub_item->sub2_comments = $sub2_comments;
            }

            $item->sub_comment = $sub_comments;
        }
        return response()->json($comments);
    }

    // Customized

    public function updatecomment(Request $request)
    {
        $comment = Comment::find($request->get('id'));
        $comment->text = $request->get('text');
        $comment->save();

        if($comment->target) {
            $comment->target->update([
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return response()->json('success');
    }

    public function count_comments(Request $request) {
        $count = Comment::where('target_id', $request->target_id)->where('target_model', $request->target_model)->count();
        return response()->json($count);
    }

}
