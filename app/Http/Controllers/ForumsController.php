<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumList;
use App\Models\ForumLike;
use App\Models\ForumBookmark;
use App\User;
use App\Models\Notification;
use Auth;

use Mail;
use App\Mail\NotificationMail;

class ForumsController extends Controller
{
    public function getActivity($updated)
    {
        $nowtime = date('Y-m-d H:i:s', time());
        $temp_time = strtotime($nowtime)-strtotime($updated);
        $different_day  = floor($temp_time/(60*60*24));
        $different_hour = floor($temp_time/(60*60));
        $different_min  = ceil($temp_time/60);
        $different_time = '';
        if($different_day>0)
        {
            if($different_day > 30)
            {
                $different_time = floor($different_day/30)."M";
            }
            else
            {
                $different_time = $different_day."D";
            }
        } else {
            if($different_hour>0) {
                $different_time = $different_hour."H";
            } else {
                if($different_min<1)
                {
                    $different_time = "1m";
                }
                else
                {
                    $different_time = $different_min."m";
                }
            }
        }
        return $different_time;
    }
    public function index(Request $request)
    {
        return view('forum.index');
    }

    public function detail(Request $request, $id) {
        $forum = ForumList::with(['user'])->find($id);
        
        $forum->description = strip_tags($forum->detail);
        return response()->json($forum);
    }

    public function create(Request $request)
    {
        if($request->id > 0) {
            $selTopic = ForumList::find($request->id);
            $selTopic->title = $request->title;
            $selTopic->category = $request->category;
            $selTopic->detail = $request->detail;
            $selTopic->save();
        } else {
            $forum = ForumList::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'slug' => convertNameToSlug($request->get('title')),
                'category' => $request->category,
                'mparent' =>  '0',
                'detail' => $request->detail,
                'updated' => date('Y-m-d H:i:s', time()),
            ]);
        }

        $topiclist = Array();
        $mod = new ForumList();
        $mod = $mod->where('mparent','0')->with('user');
        $lastpage = ForumList::where('mparent','0')->count();
        $offset = ($request->page-1)*30;
        $limit = 30;

        $allforum = $mod->orderBy('updated', 'desc')->offset($offset)->limit($limit)->get();
        foreach($allforum as $item) {
            $children_user = ForumList::where('mparent',$item->id)->where('user_id','!=',$item->user_id)->pluck('user_id')->unique()->toArray();
            $item['reply_users'] = User::whereIn('id', $children_user)->get();
            $item['time'] = $this->getActivity($item->updated);
        }
        $topiclist['allforum'] = $allforum;
        $topiclist['curtime'] = date('Y-m-d H:i:s', time());
        $topiclist['lastpage'] = $lastpage;
        if(Auth::check())
        {
            $userbookmarked = ForumBookmark::where('user_id',Auth::id())->pluck('topic_id')->unique()->toArray();
            $bookmarkedTopic = ForumList::whereIn('id', $userbookmarked)->with('user')->get();
            foreach ($bookmarkedTopic as $item) {
                if($item->mparent > 0) {
                    $item['origin'] = ForumList::find($item->mparent);
                } else {
                    $item['origin'] = "";
                }
            }
            $topiclist['bookmarkedTopic'] = $bookmarkedTopic;
        }
        else
        {
            $topiclist['bookmarkedTopic'] = "";
        }
        
        if(Auth::check()) {
            $topiclist['usertopics'] = ForumList::where('user_id',Auth::id())->with('getMParent')->orderBy('created_at', 'desc')->get();
        } else {
            $topiclist['usertopics'] = "";
        }

        return response()->json($topiclist);
    }

    public function edit(Request $request)
    {
        if($request->id > 0) {
            $selTopic = ForumList::find($request->id);
            $selTopic->title = $request->title;
            $selTopic->slug = convertNameToSlug($request->get('title'));
            $selTopic->category = $request->category;
            $selTopic->detail = $request->detail;
            $selTopic->save();
        }


        $detailpage = Array();
        $id = $request->id;

        $cur_topic = ForumList::find($id);
        $cur_topic->save();

        $curforum = ForumList::where('id',$id)->with('user')->get();
        $curforum['likeslist'] = ForumLike::where('topic_id',$id)->get();
        if(Auth::check())
        {
            $bookmark = ForumBookmark::where('topic_id',$id)->where('user_id',Auth::id())->first();
            if(!empty($bookmark))
            {
                $curforum['bookmarked'] = 1;
            } else {
                $curforum['bookmarked'] = 0;
            }
        }
        else
        {
            $curforum['bookmarked'] = 0;
        }
        if(Auth::check())
        {
            $userbookmarked = ForumBookmark::where('user_id',Auth::id())->pluck('topic_id')->unique()->toArray();
            $bookmarkedTopic = ForumList::whereIn('id', $userbookmarked)->with('user')->get();
            foreach ($bookmarkedTopic as $item)
            {
                if($item->mparent > 0)
                {
                    $item['origin'] = ForumList::find($item->mparent);
                } else {
                    $item['origin'] = "";
                }
            }
        } else {
            $bookmarkedTopic = "";
        }

        $replies= ForumList::where('mparent',$id)->where('detail','like','%'.$request->searchValue.'%')->with('user')->get();
        foreach($replies as $item) {
            if($item->getSParent){
                $sparent_user = $item->getSParent->user_id;
                $item['sParent_user'] = User::find($sparent_user);

            }
            if($item->schildren){
                $schildren= ForumList::where('sparent',$item->id)->with('user')->get()->toArray();
                $item['schildrens'] = $schildren;
            }
            $likes = ForumLike::where('topic_id',$item->id)->get();
            $item['likeslist'] = $likes;
            if(Auth::check())
            {
                $bookmark = ForumBookmark::where('topic_id',$item->id)->where('user_id',Auth::id())->first();
                if(!empty($bookmark))
                {
                    $item['bookmarked'] = 1;
                }
                else
                {
                    $item['bookmarked'] = 0;
                }
            }
            else
            {
                $item['bookmarked'] = 0;
            }
        }
        $detailpage['curforum'] = $curforum;
        $detailpage['replies'] = $replies;
        $detailpage['bookmarkedTopic'] = $bookmarkedTopic;
        $detailpage['curtime'] = date('Y-m-d H:i:s', time());
        return response()->json($detailpage);
    }

    public function delete(Request $request)
    {
        $is_main = "";
        $topicId = $request->get('topicId');
        $selTopic = ForumList::find($topicId);
        $is_main = $selTopic->mparent;
        $selTopic->delete();
        if($is_main < 1)
        {
            $allchildren = ForumList::where('mparent',$topicId)->get();
            foreach($allchildren as $item)
            {
                ForumLike::where('topic_id',$item->id)->delete();
                ForumBookmark::where('topic_id',$item->id)->delete();
                $item->delete();
            }
        }

        ForumLike::where('topic_id',$topicId)->delete();
        ForumBookmark::where('topic_id',$topicId)->delete();

        if($is_main < 1)
        {
            $detailpage = $is_main;
        } else {
            $detailpage = Array();
            $id = $topicId;

            $curforum = ForumList::where('id',$id)->with('user')->get();
            $curforum['likeslist'] = ForumLike::where('topic_id',$id)->get();
            if(Auth::check())
            {
                $bookmark = ForumBookmark::where('topic_id',$id)->where('user_id',Auth::id())->first();
                if(!empty($bookmark))
                {
                    $curforum['bookmarked'] = 1;
                }
                else
                {
                    $curforum['bookmarked'] = 0;
                }
            }
            else
            {
                $curforum['bookmarked'] = 0;
            }
            if(Auth::check())
            {
                $userbookmarked = ForumBookmark::where('user_id',Auth::id())->pluck('topic_id')->unique()->toArray();
                $bookmarkedTopic = ForumList::whereIn('id', $userbookmarked)->with('user')->get();
                foreach ($bookmarkedTopic as $item)
                {
                    if($item->mparent > 0)
                    {
                        $item['origin'] = ForumList::find($item->mparent);
                    }
                    else
                    {
                        $item['origin'] = "";
                    }
                }
            } else {
                $bookmarkedTopic = "";
            }

            $replies= ForumList::where('mparent',$id)->where('detail','like','%'.$request->searchValue.'%')->with('user')->get();
            foreach($replies as $item) {
                if($item->getSParent){
                    $sparent_user = $item->getSParent->user_id;
                    $item['sParent_user'] = User::find($sparent_user);

                }
                if($item->schildren){
                    $schildren= ForumList::where('sparent',$item->id)->with('user')->get()->toArray();
                    $item['schildrens'] = $schildren;
                }
                $likes = ForumLike::where('topic_id',$item->id)->get();
                $item['likeslist'] = $likes;
                if(Auth::check())
                {
                    $bookmark = ForumBookmark::where('topic_id',$item->id)->where('user_id',Auth::id())->first();
                    if(!empty($bookmark))
                    {
                        $item['bookmarked'] = 1;
                    }
                    else
                    {
                        $item['bookmarked'] = 0;
                    }
                }
                else
                {
                    $item['bookmarked'] = 0;
                }
            }
            $detailpage['curforum'] = $curforum;
            $detailpage['replies'] = $replies;
            $detailpage['bookmarkedTopic'] = $bookmarkedTopic;
            $detailpage['curtime'] = date('Y-m-d H:i:s', time());
        }
        return response()->json($detailpage);
    }
    public function getedittopic(Request $request)
    {
        return response()->json(ForumList::find($request->id));
    }

    public function all(Request $request)
    {  
        $catId = $request->catId;        
        $searchValue = $request->keyWord;  
        if(empty($request->keyWord))
        {
            if(!empty(session('keyword')))
            {
                $searchValue = session('keyword');
                session(['keyword' => '']);
            }
        }      
        $topiclist = Array();      
        $mod = new ForumList();
        $mod = $mod->where('mparent','0')->with('user');
        if(!empty($catId))
        {
            $mod = $mod->where('category',$catId);
        } 
        
        if(!empty($searchValue))
        {
            $user_array = User::where('name', 'like', "%$searchValue%")->pluck('id')->unique()->toArray();
            $mod = $mod->where('mparent','0')->where(function ($q) use ($searchValue, $user_array) {
                $q->where('title', 'like', "%$searchValue%")->orWhereIn('user_id', $user_array);
            });            
        }  
        
        $lastpage = ForumList::where('mparent','0')->count();
        $offset = ($request->page-1)*30;
        $limit = 30;

        $allforum = $mod->orderBy('updated', 'desc')->offset($offset)->limit($limit)->get()->makeHidden(['detail']);
        foreach($allforum as $item) {
            $children_user = ForumList::where('mparent',$item->id)->where('user_id','!=',$item->user_id)->pluck('user_id')->unique()->toArray();
            $item['reply_users'] = User::whereIn('id', $children_user)->get();
            $item['time'] = $this->getActivity($item->updated);
            $item['children'] = $item->children->makeHidden(['detail', 'getMParent']);
        }
        $topiclist['allforum'] = $allforum;
        $topiclist['curtime'] = date('Y-m-d H:i:s', time());
        $topiclist['lastpage'] = $lastpage;
        if(Auth::check()) {
            $topiclist['usertopics'] = ForumList::where('user_id',Auth::id())->with('getMParent')->orderBy('created_at', 'desc')->get()->makeHidden(['detail']);
        } else {
            $topiclist['usertopics'] = "";
        }
        
        if(Auth::check()) {
            $userbookmarked = ForumBookmark::where('user_id',Auth::id())->pluck('topic_id')->unique()->toArray();
            $bookmarkedTopic = ForumList::whereIn('id', $userbookmarked)->with('user')->get()->makeHidden(['detail']);
            foreach ($bookmarkedTopic as $item) {
                if($item->mparent > 0) {
                    $item['origin'] = ForumList::find($item->mparent);
                } else {
                    $item['origin'] = "";
                }
            }
            $topiclist['bookmarkedTopic'] = $bookmarkedTopic;
        } else {
            $topiclist['bookmarkedTopic'] = "";
        }

        return response()->json($topiclist);
    }

    public function replycreate(Request $request)
    {              
        if($request->createdit == false) {
            $selTopic = ForumList::find($request->id);
            $selTopic->detail = $request->detail;
            $selTopic->save();            
        } else {
            $forum = ForumList::create([
                'user_id' => Auth::id(),
                'title' => "",
                'category' => "",
                'detail' => $request->detail,
                'mparent' => $request->mparent,
                'sparent' => $request->sparent,
            ]);
            $parent = $forum->getSparent ?? $forum->getMparent;
            if(Auth::id() != $parent->user_id) {                
                $notification = Notification::create([
                    'type' => 'reply_topic',
                    'notifier_id' => Auth::id(),
                    'user_id' => $parent->user_id ?? '',
                    'notifiable_id' => $parent->id ,
                    'notifiable_type' => 'App\Models\ForumList',
                ]);
                if($notification->user && $notification->user->check_notification_filter('comment_reply')){                  
                    $toEmail = $notification->user->email;
                    try {
                        Mail::to($toEmail)->send(new NotificationMail($notification));  
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
            }
        }


        $detailpage = Array();
        $id = $request->mparent;
        $cur_topic = ForumList::find($id);
        $cur_topic->replies = $cur_topic->replies + 1;
        $cur_topic->updated = date('Y-m-d H:i:s', time());
        $cur_topic->save();

        $curforum = ForumList::where('id',$id)->with('user')->get();
        $curforum['likeslist'] = ForumLike::where('topic_id',$id)->get();

        if(Auth::check())
        {
            $bookmark = ForumBookmark::where('topic_id',$id)->where('user_id',Auth::id())->first();
            if(!empty($bookmark))
            {
                $curforum['bookmarked'] = 1;
            }
            else
            {
                $curforum['bookmarked'] = 0;
            }
        }
        else
        {
            $curforum['bookmarked'] = 0;
        }

        $userbookmarked = ForumBookmark::where('user_id',Auth::id())->pluck('topic_id')->unique()->toArray();
        $bookmarkedTopic = ForumList::whereIn('id', $userbookmarked)->with('user')->get();
        foreach ($bookmarkedTopic as $item)
        {
            if($item->mparent > 0)
            {
                $item['origin'] = ForumList::find($item->mparent);
            }
            else
            {
                $item['origin'] = "";
            }
        }


        $replies= ForumList::where('mparent',$id)->with('user')->get();
        foreach($replies as $item) {
            if($item->getSParent){
                $sparent_user = $item->getSParent->user_id;
                $item['sParent_user'] = User::find($sparent_user);
            }
            if($item->schildren){
                $schildren= ForumList::where('sparent',$item->id)->with('user')->get()->toArray();
                $item['schildrens'] = $schildren;
            }
            $likes = ForumLike::where('topic_id',$item->id)->get();
            $item['likeslist'] = $likes;
            if(Auth::check())
            {
                $bookmark = ForumBookmark::where('topic_id',$item->id)->where('user_id',Auth::id())->first();
                if(!empty($bookmark)) {
                    $item['bookmarked'] = 1;
                } else {
                    $item['bookmarked'] = 0;
                }
            }
            else
            {
                $item['bookmarked'] = 0;
            }
        }
        $detailpage['curforum'] = $curforum;
        $detailpage['replies'] = $replies;
        $detailpage['bookmarkedTopic'] = $bookmarkedTopic;
        $detailpage['curtime'] = date('Y-m-d H:i:s', time());
        if(Auth::check())
        {
            $detailpage['usertopics'] = ForumList::where('user_id',Auth::id())->with('getMParent')->orderBy('created_at', 'desc')->get();
        }
        else
        {
            $detailpage['usertopics'] = "";
        }
        return response()->json($detailpage);
    }
    
    public function setkeyword(Request $request)
    {
        $keyword = $request->searchValue;
        session(['keyword' => $keyword]);
        return response()->json("ok");
    }

    public function getdetail(Request $request, $forumid) {
        $user_id = Auth::id();
        $detailpage = Array();
        $id = $forumid;

        $cur_topic = ForumList::find($id);
        $cur_topic->increment('views');

        $curforum = ForumList::with('user')->where('id',$id)->get();
        $curforum['likeslist'] = ForumLike::where('topic_id',$id)->get();
        $curforum['is_like'] = $cur_topic->likes()->where('user_id', $user_id)->exists();
        $curforum['time'] = $this->getActivity($cur_topic->updated);
        if(Auth::check()) {
            $bookmark = ForumBookmark::where('topic_id',$id)->where('user_id',Auth::id())->first();
            if(!empty($bookmark)) {
                $curforum['bookmarked'] = 1;
            } else {
                $curforum['bookmarked'] = 0;
            }
        } else {
            $curforum['bookmarked'] = 0;
        }
        if(Auth::check()) {
            $userbookmarked = ForumBookmark::where('user_id',Auth::id())->pluck('topic_id')->unique()->toArray();
            $bookmarkedTopic = ForumList::whereIn('id', $userbookmarked)->with('user')->get();
            foreach ($bookmarkedTopic as $item)
            {
                if($item->mparent > 0) {
                    $item['origin'] = ForumList::find($item->mparent);
                } else {
                    $item['origin'] = "";
                }
            }
        } else {
            $bookmarkedTopic = "";
        }

        $replies= ForumList::where('mparent',$id)->where('detail','like','%'.$request->searchValue.'%')->with('user')->get();
        foreach($replies as $item) {
            if($item->getSParent){
                $sparent_user = $item->getSParent->user_id;
                $item['sParent_user'] = User::find($sparent_user);

            }
            if($item->schildren){
                $schildren= ForumList::where('sparent',$item->id)->with('user')->get()->toArray();
                $item['schildrens'] = $schildren;
            }
            $likes = ForumLike::where('topic_id',$item->id)->get();
            $item['likeslist'] = $likes;
            $item['is_like'] = $item->likes()->where('user_id', $user_id)->exists();
            $item['time'] = $this->getActivity($item->updated_at);
            if(Auth::check()) {
                $bookmark = ForumBookmark::where('topic_id',$item->id)->where('user_id',Auth::id())->first();
                if(!empty($bookmark))
                {
                    $item['bookmarked'] = 1;
                }
                else
                {
                    $item['bookmarked'] = 0;
                }
            } else {
                $item['bookmarked'] = 0;
            }
        }
        if(Auth::check()) {
            if(Auth::user()->name == "420portal")
            {
                $isAdmin = true;
            }
            else
            {
                $isAdmin = false;
            }
        } else {
            $isAdmin = false;
        }

        $detailpage['curforum'] = $curforum;
        $detailpage['replies'] = $replies;
        $detailpage['bookmarkedTopic'] = $bookmarkedTopic;
        $detailpage['curtime'] = date('Y-m-d H:i:s', time());
        $detailpage['isAdmin'] = $isAdmin;
        if(Auth::check()) {
            $detailpage['usertopics'] = ForumList::where('user_id',Auth::id())->with('getMParent')->orderBy('created_at', 'desc')->get();
        } else {
            $detailpage['usertopics'] = "";
        }
        return response()->json($detailpage);
    }

    public function like(Request $request)
    {
        $topic = ForumList::find($request->likedid);
        $check = ForumLike::where('user_id',Auth::id())->where('topic_id',$request->likedid)->first();
        if(!empty($check))
        {
            if($topic->likes < 1)
            {
                $topic->likes = 0;
            } else {
                $topic->likes = $topic->likes-1;
            }
            $topic->save();
            $check->delete();
            return response()->json(['status' => 'dislike', 'like' => $check]);
        } else {
            $topic->increment('likes');
            $like = ForumLike::create([
                'user_id' => Auth::id(),
                'topic_id' => $request->likedid,
            ]);

            if(Auth::id() != $like->topic->user_id) {                
                $notification = Notification::create([
                    'type' => 'like_topic',
                    'notifier_id' => Auth::id(),
                    'user_id' => $like->topic->user_id ?? '',
                    'notifiable_id' => $like->topic_id ,
                    'notifiable_type' => 'App\Models\ForumList',
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

            return response()->json(['status' => 'like', 'like' => $like]);
        }

        
    }

    public function bookmark(Request $request)
    {
        $check = ForumBookmark::where('user_id',Auth::id())->where('topic_id',$request->bookmarkedid)->first();
        if(!empty($check))
        {
            $check->delete();
        } else {
            $bookmark = ForumBookmark::create([
                'user_id' => Auth::id(),
                'topic_id' => $request->bookmarkedid,
            ]);
        }

        return response()->json("ok");
    }

    public function gettopicuser($user_id)
    {
        $info = array();
        $user = User::find($user_id);
        if(!empty($user))
        {
            $info['name'] = $user->name;
            if($user->profilePic)
            {
                $info['pic'] = $user->profilePic->url;
            }
            else
            {
                $info['pic'] = "";
            }
        }                
        else
        {
            $info = "";
        }
        return response()->json($info);
    }
}
