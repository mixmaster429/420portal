<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ForumList;
use App\Models\ForumLike;
use App\Models\ForumBookmark;
use App\User;
use App\Models\Notification;
use Auth;

use Mail;
use App\Mail\NotificationMail;

class ForumController extends Controller
{
    public function index(Request $request) {
        $user_topics = $user_bookmarked = [];
        $mod = new ForumList();
        $mod = $mod->with('user')->where('mparent', 0);
        if($request->get('category') != '') {
            $mod = $mod->where('category', $request->get('category'));
        }
        if($request->get('keyword') != '') {
            $keyword = $request->get('keyword');

            $user_array = User::where('name', 'like', "%$keyword%")->distinct()->pluck('id')->toArray();
            $mod = $mod->where(function ($query) use ($keyword, $user_array) {
                return $query->where('title', 'like', "%$keyword%")
                            ->orWhereIn('user_id', $user_array);
            });      
        }
        $forum_lists = $mod->orderBy('updated', 'desc')->paginate(15);

        foreach($forum_lists as $item) {
            $item['activity_time'] = $this->getActivityTime($item->updated);
            $item['detail'] = '';
        }

        $data = [
            'status' => 200,
            'data' => [
                'forum_lists' => $forum_lists,
            ],
        ];
        return response()->json($data);
    }

    public function getUserTopics(){
        $user_topics = ForumList::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();    
    
        $bookmarked_array = ForumBookmark::where('user_id', auth()->id())->distinct()->pluck('topic_id')->toArray();
        $user_bookmarked = ForumList::with('user')->whereIn('id', $bookmarked_array)->get();
        $data = [
            'status' => 200,
            'data' => [
                'user_topics' => $user_topics,
                'user_bookmarked' => $user_bookmarked,
            ],
        ];
        return response()->json($data);
    }

    public function getDetail(Request $request)
    {
        $user_id = auth()->id();
        $id = $request->get('id');

        $topic = ForumList::find($id);

        $topic = ForumList::with('user')->withCount('likes')->find($id);
        $topic->increment('views');

        $topic['is_like'] = $topic->likes()->where('user_id', $user_id)->exists();
        $topic['is_bookmark'] = ForumBookmark::where('topic_id', $id)->where('user_id', $user_id)->exists();
        $topic['activity_time'] = $this->getActivityTime($topic->updated);       
        $keyword = $request->get('keyword');
        $topic_replies= $topic->m_children()->with('user')->withCount('likes')->where('detail', 'like', "%$keyword%")->get();
        foreach($topic_replies as $item) {
            if($item->s_parent) {
                $item->s_parent->load('user');
            }
            $item->s_children = $item->s_children()->with('user')->get();
            $item['is_like'] = $item->likes()->where('user_id', $user_id)->exists();
            $item['is_bookmark'] = ForumBookmark::where('topic_id', $item->id)->where('user_id', $user_id)->exists();
            $item['activity_time'] = $this->getActivityTime($item->updated);
        }
        $topic['replies'] = $topic_replies;

        $data = [
            'status' => 200,
            'data' => $topic,
            'current_time' => date('Y-m-d H:i:s'),
        ];
        return response()->json($data);
    }

    public function like(Request $request) {
        $id = $request->get('id');
        $topic = ForumList::find($id);
        $like_item = ForumLike::where('user_id', auth()->id())->where('topic_id', $id)->first();
        if($like_item) {
            if($topic->likes < 1) {
                $topic->likes = 0;
            } else {
                $topic->likes = $topic->likes - 1;
            }
            $topic->save();
            $like_item->delete();
            $data = [
                'status' => 200,
                'is_like' => false,
                'likes_count' => ForumLike::where('topic_id', $id)->count(),
            ];
            return response()->json($data);
        } else {
            $topic->increment('likes');
            $like = ForumLike::create([
                'user_id' => auth()->id(),
                'topic_id' => $id,
            ]);

            if(auth()->id() != $like->topic->user_id) {                
                $notification = Notification::create([
                    'type' => 'like_topic',
                    'notifier_id' => auth()->id(),
                    'user_id' => $like->topic->user_id ?? '',
                    'notifiable_id' => $like->topic_id,
                    'notifiable_type' => 'App\Models\ForumList',
                ]);
                if($notification->user && $notification->user->check_notification_filter('like')){                  
                    $toEmail = $notification->user->email;
                    // Mail::to($toEmail)->send(new NotificationMail($notification));  
                }
            }
            $data = [
                'status' => 200,
                'is_like' => true,
                'likes_count' => ForumLike::where('topic_id', $id)->count(),
            ];
            return response()->json($data);
        }

        
    }

    public function bookmark(Request $request) {
        $id = $request->get('id');
        $check = ForumBookmark::where('user_id', auth()->id())->where('topic_id', $id)->first();
        $data = ['status' => 200];
        if(!empty($check)) {
            $check->delete();
            $data['is_bookmark'] = false;
        } else {
            $bookmark = ForumBookmark::create([
                'user_id' => auth()->id(),
                'topic_id' => $id,
            ]);
            $data['is_bookmark'] = true;
        }
        return response()->json($data);
    }

    public function create(Request $request) {
        $forum = ForumList::create([
            'user_id' => auth()->id(),
            'title' => $request->get('title'),
            'slug' => convertNameToSlug($request->get('title')),
            'category' => $request->get('category'),
            'mparent' =>  '0',
            'detail' => $request->get('detail'),
            'updated' => date('Y-m-d H:i:s'),
        ]);
        return response()->json(['status' => 200]);
    }

    public function edit(Request $request) {
        $topic = ForumList::find($request->get('id'));
        if($topic->mparent == 0) {
            $topic->title = $request->get('title');
            $topic->slug = convertNameToSlug($request->get('title'));
            $topic->category = $request->get('category');
        }
        $topic->detail = $request->get('detail');
        $topic->updated = date('Y-m-d H:i:s');
        if($topic->m_parent) {
            $topic->m_parent->update(['updated_at' => date('Y-m-d H:i:s')]);
        }
        $topic->save();
        return response()->json(['status' => 200]);
    }

    public function reply(Request $request) {
        $reply = new ForumList();
        $parent = ForumList::find($request->get('parent_id'));
        if($parent->mparent) {
            $reply->mparent = $parent->mparent;
            $reply->sparent = $parent->id;
        } else {
            $reply->mparent = $parent->id;
        }
        $reply->user_id = auth()->id();
        $reply->updated = date('Y-m-d H:i:s');
        $reply->detail = $request->get('detail');
        $reply->save();
        $m_parent = $reply->m_parent;
        $m_parent->replies = $m_parent->m_children()->count();
        $m_parent->save();
        return response()->json(['status' => 200]);
    }

    public function delete($id) {
        $is_main = "";
        $item = ForumList::find($id);
        $is_main = $item->mparent > 0 ? false : true;
        $item->delete();
        if($is_main) {
            $all_children = ForumList::where('mparent', $id)->get();
            foreach($all_children as $item) {
                ForumLike::where('topic_id', $item->id)->delete();
                ForumBookmark::where('topic_id', $item->id)->delete();
                $item->delete();
            }
        } else {
            $m_parent = $item->m_parent;
            $m_parent->replies = $m_parent->m_children()->count();
            $m_parent->save();
        }

        ForumLike::where('topic_id', $id)->delete();
        ForumBookmark::where('topic_id', $id)->delete();

        return response()->json(['status' => 200]);
    }
































    public function getActivityTime($updated) {
        $nowtime = date('Y-m-d H:i:s', time());
        $temp_time = strtotime($nowtime)-strtotime($updated);
        $different_day  = floor($temp_time/(60*60*24));
        $different_hour = floor($temp_time/(60*60));
        $different_min  = ceil($temp_time/60);
        $different_time = '';
        if($different_day > 0) {
            if($different_day > 30) {
                $different_time = floor($different_day/30)."M";
            } else {
                $different_time = $different_day."D";
            }
        } else {
            if($different_hour > 0) {
                $different_time = $different_hour."H";
            } else {
                if($different_min < 1) {
                    $different_time = "1m";
                } else {
                    $different_time = $different_min."m";
                }
            }
        }
        return $different_time;
    }
}
