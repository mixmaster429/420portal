<?php

namespace App\Http\Controllers;

use App\Models\BlockUser;
use App\Models\Follow;
use App\Models\Like;
use App\Models\Media;
use App\Models\Portal;
use App\Models\Brand;
use App\Models\Save;
use App\Models\UserChat;
use App\Models\Comment;
use App\Models\Notification;
use App\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Image;
use Mail;
use App\Mail\NotificationMail;

class UserController extends Controller
{
    public function index($slug) {
        $auth_id = auth()->id();
        $user = User::where('username', $slug)->first();
        if(!$user) {return response()->json(['status' => 404, 'message' => 'User not found!']);}
        if($user->is_active != 1 && ($auth_id != $user->id && $auth_id != 1)) {
            return response()->json(['status' => 400, 'message' => 'Inactive user!']);
        }
        if ($user->type == 'user') {
            $user->load('medias', 'profilePic');
            return response()->json(['status' => 200, 'profile' => $user]);
        } else {
            // check private
            if(auth()->id()) {
                $follow_users = Follow::where('user_id', auth()->id())->distinct()->pluck('follower_user_id')->toArray();
                $private_users = User::where('is_private', 1)->whereNotIn('id', $follow_users)->pluck('id')->toArray();
            } else {
                $private_users = User::where('is_private', 1)->pluck('id')->toArray();
            }

            $user->posts_count = Media::where('is_active', 1)->whereNotIn('user_id', $private_users)
                    ->where(function($query) use($user) {
                        return $query->where('user_id', $user->id)
                                ->orWhere('tagged_portal', $user->id);
                    })->count();

            $user->total_comments = Comment::where('target_id', $user->id)->where('target_model', 'portal')->count();
            $title = $user->name;
            if($user->type == 'company') {
                $user->shop_status = $user->get_shop_status();
                if($user->store_type == 3) {
                    $title = $user->name." Marijuana Dispensary - Delivery";
                } else if($user->store_type == 1) {
                    $title = $user->name." Marijuana Dispensary";
                } else if($user->store_type == 2) {
                    $title = $user->name." Marijuana Delivery";
                }
            }
            $user->title_tag = $title;
            $user->load('profilePic', 'medias', 'taggedMedia', 'taggedPortalMedia', 'coupon', 'menus');
            return response()->json(['status' => 200, 'profile' => $user]);
        }
        return response()->json(['status' => 404, 'message' => 'User not found!']);
    }

    public function dashboard() {
        if(Auth::user()->name != '420portal') {
            return redirect('/')->withErrors(['user' => 'No permission']);
        }
        return view('User.dashboard', ['user' => Auth::user()]);
    }

    public function getallpost(Request $request) {
        $user_id = $request->get('currentId') ?? auth()->id();
        $auth_id = auth()->id();
        $user = User::find($user_id);
        
        $model = $request->get('model');
        $target = $request->get('target_id');

        // return response()->json($request->method());
        if($request->method() == 'OPTIONS') {
            return response()->json('success');
        }
        if ($model == 'portal') {
            // check private
            if(auth()->id()) {
                $follow_users = Follow::where('user_id', $auth_id)->distinct()->pluck('follower_user_id')->toArray();
                $private_users = User::where('is_private', 1)->whereNotIn('id', $follow_users)->pluck('id')->toArray();
            } else {
                $private_users = User::where('is_private', 1)->pluck('id')->toArray();
            }
            $allposts = Media::with('menu')->where('is_active', 1)->whereNotIn('user_id', $private_users)
                ->where(function($query) use($target) {
                    return $query->where('user_id', $target)
                            ->orWhere('tagged_portal', $target);
                    })
                ->orderBy('created_at', 'desc')->paginate(18);
        } elseif ($model == 'brand') {
            $brand = User::find($target);
            if(auth()->id()) {
                $follow_users = Follow::where('user_id', $auth_id)->distinct()->pluck('follower_user_id')->toArray();
                $private_users = User::where('is_private', 1)->whereNotIn('id', $follow_users)->pluck('id')->toArray();
            } else {
                $private_users = User::where('is_private', 1)->pluck('id')->toArray();
            }
            $brand_menu_media_array = $brand->menus()->where('is_active', 1)->pluck('media_id')->toArray();
            // if($brand->profilePic) {
            //     array_push($brand_menu_media_array, $brand->media_id);
            // }
 
            $allposts = Media::with('menu')->where('is_active', 1)->where('user_id', $target)->orderBy('created_at', 'desc')->paginate(18);
            // dd($allposts);
        } elseif ($model == 'strain') {
            $allposts = Media::with('strain')->where('is_active', 1)->where('tagged_strain', $target)->orderBy('created_at', 'desc')->paginate(18);
            return response()->json($allposts);
        } else {
            $userTagged = $user->taggedMedia()->where('is_active', 1)->pluck('media_id');
            $userMedia = $user->medias()->whereIn('model', ['post', 'profile'])->where('is_active', 1);
            $allposts = $userMedia->orWhereIn('id', $userTagged)->orderByDesc('created_at')->paginate(18);

            $user['media'] = $user->medias()->first();
            $user['profile_pic'] = $user->profilePic;
            $user->profilePic;
        }

        foreach ($allposts as $key => $item) {
            auth()->id() && $item['loged_user'] = 1;
            $item->user->profilePic;

            if ($item->tagged_strain) {
                $strain = $item->strain;
                if ($strain) {
                    $item->strain->media;
                }
            }

            $item['tagged_usersData'] = $item->taggedUsers;

            if ($item->taggedStrain) {
                $item['tagged_strainData'] = [$item->taggedStrain];
            } else {
                $item['tagged_strainData'] = [];
            }
            if ($item->taggedPortal) {
                $item['tagged_companyData'] = [$item->taggedPortal];
            } else {
                $item['tagged_companyData'] = [];
            }

            $media_id = $item['id'];
            $likes = Like::where('target_id', $media_id)->where('model', 'post')->count();
            $item['likes'] = $likes;
            $user_liked = Like::where('user_id', $auth_id)->where('target_id', $media_id)->where('model', 'post')->count();
            $item['user_liked'] = $user_liked;

            $user_saved = Save::where('user_id', $auth_id)->where('media_id', $media_id)->count();
            $item['user_saved'] = $user_saved;
            // Added
            if(Follow::where('user_id', $user_id)->where('follower_user_id', $item['user_id'])->count() == 0)
                $item['isfollower'] = 0;
            else
                $item['isfollower'] = 1;
            $count_comment = $item->comments()->count();
            $item['count_comment'] = $count_comment;
            $item['description_expanded'] = false;
        }

        $return = [];
        $return['userdata'] = $user;
        $return['postdata'] = $allposts;

        return response()->json($return);
    }

    public function update(Request $request) {
        $user = User::find($request->get('id'));
        $media_url = $request->profile_logourl;
        $media_type = $request->profile_mediatype;
        $newImage = $request->image;
        $description = $request->description;
        $name = str_replace(" ", "", $request->name);
        $email = $request->email;

        if (null !== $newImage) {
            $media = new Media();

            $destinationPath = 'uploaded/image';
            $ImageUpload = Image::make($newImage)->resize(600, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->orientate();

            $ext = $newImage->getClientOriginalExtension();

            $filename = get_filename(Auth::user()->name)."_marijuana_".time().'.'.$ext;

            $ImageUpload->save($destinationPath.'/'.$filename, 85);

            $media->url = '/'.$destinationPath.'/'.$filename;
            $media->type = 'image';
            $media->user_id = $user->id;
            $media->model = 'profile';
            $media->save();
            $media_id = $media->id;
            $user->media_id = $media_id;
        }

        $user->description = $description;
        $user->username = $name;
        $user->name = $name;
        $user->email = $email;
        $user->is_private = $request->get('is_private');

        $user->save();

        return response()->json(['status' => 200, 'user' => $user]);
    }

    public function chatlist() {
        $user_id = auth()->id();
        $relate_user1 = UserChat::where('user_id', $user_id)->where('deleted_by', '!=', $user_id)->pluck('user_to')->toarray();
        $relate_user2 = UserChat::where('user_to', $user_id)->where('deleted_by', '!=', $user_id)->pluck('user_id')->toarray();
        $relate_user = array_unique(array_merge($relate_user1, $relate_user2));

        // check private
        $follow_users = Follow::where('user_id', auth()->id())->distinct()->pluck('follower_user_id')->toArray();
        $private_users = User::where('is_private', 1)->whereNotIn('id', $follow_users)->pluck('id')->toArray();

        $users = User::whereIn('id', $relate_user)->whereNotIn('id', $private_users)->get()->except($user_id);
        $result = [];
        foreach ($users as $user) {
            $block = BlockUser::where('loggeduser', $user_id)->where('blockeduser', $user->id)->count();
            if($block == 0){
                $lastmessage = UserChat::whereIn('user_id', [$user_id, $user->id])->whereIn('user_to', [$user_id, $user->id])->orderBy('created_at', 'desc')->first();
                if(!$lastmessage || $lastmessage->deleted_by == $user_id){
                    $user->lastmessage = '';
                    $user->last_time = 0;
                }else{
                    $user->lastmessage = $lastmessage;
                    $user->last_time = strtotime($lastmessage->created_at);
                }
                $user->url = $user->profilePic->url ?? '/imgs/default_sm.png';
                $unreadcount = UserChat::where('user_id', $user->id)->where('user_to', $user_id)->where('read', 0)->count();
                $user->unread_count = $unreadcount;
                array_push($result, $user);
            }
        }
        $collection = collect($result)->sortByDesc(function($user) {
            return $user->last_time;
        })->values()->all();
        return response()->json($collection);
    }

    public function togglemessenger()
    {
        $user = auth()->user();
        $messenger = $user->messenger;
        $messenger = !$messenger;
        $user->messenger = $messenger;
        $user->save();

        $portals = Portal::where('user_id', $user->id)->get();
        foreach($portals as $portal){
            $portal->messenger = !$portal->messenger;
            $portal->save();
        }

        return response()->json(['status' => $messenger]);
    }

    public function deletemessage(Request $request)
    {
        $sender = $request->get('sender');
        $receiver = $request->get('receiver');
        $sender_chats = UserChat::where('user_id', $sender)->where('user_to', $receiver)->get();
        foreach($sender_chats as $chat){
            if($chat->deleted_by != 0){
                $chat->delete();
            }else{
                $chat->deleted_by = $sender;
                $chat->save();
            }
        }
        $receiver_chats = UserChat::where('user_to', $sender)->where('user_id', $receiver)->get();
        foreach($receiver_chats as $chat){
            if($chat->deleted_by != 0){
                $chat->delete();
            }else{
                $chat->deleted_by = $sender;
                $chat->save();
            }
        }
        return response()->json(['status' => 1]);
    }

    public function searchuser(Request $request) {
        $keyword = $request->get('keyword');
        $user_id = auth()->id();

        // check private
        if(auth()->id()) {
            $follow_users = Follow::where('user_id', auth()->id())->distinct()->pluck('follower_user_id')->toArray();
            $private_users = User::where('is_private', 1)->whereNotIn('id', $follow_users)->pluck('id')->toArray();
        } else {
            $private_users = User::where('is_private', 1)->pluck('id')->toArray();
        }

        $userlist = User::where('name', 'like', '%'.$keyword.'%')->whereNotIn('id', $private_users)->where('messenger', 1)->get()->except($user_id);

        $result = [];
        foreach($userlist as $user)
        {
            $block = BlockUser::where('loggeduser', $user_id)->where('blockeduser', $user->id)->count();

            if ($block == 0) {
                $tmp = [];
                $tmp['id'] = $user->id;
                $tmp['name'] = $user->name;
                $tmp['username'] = $user->name;
                $tmp['url'] = $user->profilePic->url ?? '/imgs/default_sm.png';
                $unread = UserChat::where('user_id', $user->id)->where('user_to', $user_id)->where('read', 0)->count();
                $tmp['unread_count'] = $unread;
                $tmp['type'] = $user->type;
                $tmp['store_type'] = $user->store_type;
                $lastmessage = UserChat::whereIn('user_id', [$user_id, $user->id])->whereIn('user_to', [$user_id, $user->id])->orderBy('created_at', 'desc')->first();
                if(!$lastmessage || $lastmessage->deleted_by == $user_id){
                    $tmp['lastmessage'] = '';
                }else{
                    $tmp['lastmessage'] = $lastmessage;
                }

                array_push($result, $tmp);
            }
        }

        return response()->json($result);
    }

    public function searchuseronmobile(Request $request) {
        $keyword = $request->get('keyword');
        $user_id = auth()->id();

        // check private
        if(auth()->id()) {
            $follow_users = Follow::where('user_id', auth()->id())->distinct()->pluck('follower_user_id')->toArray();
            $private_users = User::where('is_private', 1)->whereNotIn('id', $follow_users)->pluck('id')->toArray();
        } else {
            $private_users = User::where('is_private', 1)->pluck('id')->toArray();
        }

        $userlist = User::where('name', 'like', '%'.$keyword.'%')->whereNotIn('id', $private_users)->where('messenger', 1)->get()->except($user_id);

        $result = [];
        foreach($userlist as $user)
        {
            $tmp = [];
            $tmp['id'] = $user->id;
            $tmp['name'] = $user->name;
            $tmp['username'] = $user->username;
            $tmp['url'] = $user->profilePic ? $user->profilePic->url : '/imgs/default_sm.png';
            $unread = UserChat::where('user_id', $user->id)->where('user_to', $user_id)->where('read', 0)->count();
            $tmp['unread'] = $unread;

            array_push($result, $tmp);
        }

        return response()->json($result);
    }

    public function getMessengerStatus() {
        $user_id = auth()->id();
        $messenger_status = User::find($user_id)->messenger;
        return response()->json($messenger_status);
    }

    public function block(Request $request)
    {
        $logged_id = auth()->id();
        $blockuserid = $request->get('blockuserid');
        BlockUser::create([
            'loggeduser' => $logged_id,
            'blockeduser' => $blockuserid
        ]);
        return response()->json(['status' => 1]);
    }

    public function enableblock(Request $request)
    {
        $logged_id = auth()->id();
        $blockuserid = $request->get('blockuserid');
        $blockuser = BlockUser::where('loggeduser', $logged_id)->where('blockeduser', $blockuserid)->first();
        $blockuser->delete();
        return response()->json(['status' => 1]);
    }

    public function isblockuser(Request $request)
    {
        $logged_id = auth()->id();
        $blockuserid = $request->get('receiver');
        $user_array = [$logged_id, $blockuserid];
        $blockuser = BlockUser::whereIn('blockeduser', $user_array)->whereIn('loggeduser', $user_array)->count();
        if($blockuser)
            return 1;
        else
            return 0;
    }

    public function blockuserlist()
    {
        $logged_id = auth()->id();
        $userids = BlockUser::where('loggeduser', $logged_id)->get();
        $result = [];
        foreach($userids as $userid)
        {
            $tmp = [];
            $user = User::find($userid->blockeduser);
            $tmp['id'] = $user->id;
            $tmp['name'] = $user->name;
            $tmp['username'] = $user->username;
            $tmp['url'] = $user->profilePic->url ?? '/imgs/default_sm.png';
            $tmp['type'] = $user->type;
            $tmp['store_type'] = $user->store_type;

            $lastmessage = UserChat::whereIn('user_id', [$logged_id, $user->id])->whereIn('user_to', [$logged_id, $user->id])->orderBy('created_at', 'desc')->first();
            if($lastmessage && $lastmessage->deleted_by == $logged_id){
                $tmp['lastmessage'] = '';
            }else{
                $tmp['lastmessage'] = $lastmessage;
            }

            array_push($result, $tmp);
        }
        return response()->json($result);
    }
    public function blockuserlistonmobile()
    {
        $logged_id = auth()->id();
        $userids = BlockUser::where('loggeduser', $logged_id)->get();
        $result = [];
        foreach($userids as $userid) {
            $tmp = [];
            $user = User::find($userid->blockeduser);
            $tmp['id'] = $user->id;
            $tmp['name'] = $user->name;
            $tmp['username'] = $user->username;
            $tmp['url'] = $user->profilePic->url ?? '/imgs/default.png';
            $tmp['type'] = $user->type;
            $tmp['store_type'] = $user->store_type;
            array_push($result, $tmp);
        }
        return response()->json($result);
    }

    public function api() {
        $user = User::where('type', 'user')->get()->except(Auth::id());
        return response()->json($user);
    }

    /* follow feature by David */
    public function follow(Request $request) {
        $user_id = $request->get('user_id');
        $follower_id = $request->get('follower_id');
        $follow = Follow::where('user_id', $user_id)->where('follower_user_id', $follower_id)->first();
        if(!$follow) {            
            Follow::create([
                'user_id' => $user_id,
                'follower_user_id' => $follower_id
            ]);
            if($user_id != $follower_id) {          
                $notification = Notification::create([
                    'type' => 'follow',
                    'notifier_id' => $user_id,
                    'user_id' => $follower_id,
                    'notifiable_id' => $user_id,
                    'notifiable_type' => 'App\User',
                ]);
                if($notification->user && $notification->user->check_notification_filter('follow')){
                    $toEmail = $notification->user->email;
                    try {
                        Mail::to($toEmail)->send(new NotificationMail($notification));
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }  
            }
        }

        return response()->json(['status' => 1]);
    }

    public function unfollow(Request $request) {
        $user_id = $request->user_id;
        $follower_id = $request->follower_id;
        $record = Follow::where('user_id', $user_id)->where('follower_user_id', $follower_id)->first();
        $record->delete();
        return response()->json(['status' => 1]);
    }

    public function followRequest(Request $request) {
        $user_id = $request->get('user_id');
        $follower_id = $request->get('follower_id');
        if($user_id != $follower_id) {          
            $notification = Notification::create([
                'type' => 'follow_request',
                'notifier_id' => $user_id,
                'user_id' => $follower_id,
                'notifiable_id' => $user_id,
                'notifiable_type' => 'App\User',
            ]);
            if($notification->user && $notification->user->check_notification_filter('follow')){
                $toEmail = $notification->user->email;
                try {
                   Mail::to($toEmail)->send(new NotificationMail($notification));
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
            return response()->json(['status' => 2]);
        }
    }

    public function acceptFollowRequest(Request $request) {
        $notification = Notification::find($request->get('notification_id'));
        $follow = Follow::where('user_id', $notification->notifier_id)->where('follower_user_id', $notification->user_id)->first();
        if(!$follow) {            
            Follow::create([
                'user_id' => $notification->notifier_id,
                'follower_user_id' => $notification->user_id,
            ]);
            $notification->delete();
        }
        return response()->json(['status' => 200]);
    }

    public function getisfollower(Request $request) {
        $user_id = $request->user_id;
        $follower_user_id = $request->follower_user_id;
        $follower = User::find($follower_user_id);
        $count = Follow::where('user_id', $user_id)->where('follower_user_id', $follower_user_id)->count();
        $data = ['status' => 0];
        if($count == 0) {
            if($follower->is_private) {
                $follow_requested = Notification::where('user_id', $follower_user_id)->where('notifier_id', $user_id)->whereType('follow_request')->exists();
                if($follow_requested) {
                    $data['status'] = 2;
                }
            }
        } else {
            $data['status'] = 1;
        }            
        return response()->json($data);
    }

    public function getfollow(Request $request) {
        $user_id = $request->get('user_id');
        $followers = Follow::where('follower_user_id', $user_id)->count();
        $following = Follow::where('user_id', $user_id)->whereNotNull('follower_user_id')->count();
        $result = [];
        $result['followers'] = $followers;
        $result['following'] = $following;
        return response()->json($result);
    }

    public function allfollows(Request $request) {
        $user_id = $request->user_id;
        $follower_user_ids = Follow::where('follower_user_id', $user_id)->pluck('user_id');

        $followers = [];
        foreach($follower_user_ids as $id){
            $tmp = [];
            $user = User::find($id);
            $tmp['id'] = $user->id;
            $tmp['name'] = $user->name;
            $tmp['username'] = $user->username;
            $tmp['type'] = $user->type;
            $tmp['store_type'] = 0;
            $tmp['url'] = $user->profilePic ? $user->profilePic->url : '/imgs/default_sm.png';
            $tmp['isfollower'] = 0;
            if(auth()->user()){
                $logged_id = auth()->id();
                $follower_user_count = Follow::where('user_id', $logged_id)->where('follower_user_id', $user->id)->count();
                if($follower_user_count != 0) {
                    $tmp['isfollower'] = 1;
                } else {
                    if($user->is_private) {
                        $follow_requested = Notification::where('user_id', $id)->where('notifier_id', $logged_id)->whereType('follow_request')->exists();
                        if($follow_requested) {
                            $tmp['isfollower'] = 2;
                        }
                    }
                }
            }

            $tmp['isportal'] = 0;

            array_push($followers, $tmp);
        }

        $following_user_ids = Follow::where('user_id', $user_id)->pluck('follower_user_id');
        $followings = [];
        foreach($following_user_ids as $id){
            if(!is_null($id)){
                $tmp = [];
                $user = User::find($id);
                $tmp['id'] = $user->id;
                $tmp['name'] = $user->name;
                $tmp['username'] = $user->username;
                $tmp['type'] = $user->type;
                $tmp['store_type'] = $user->store_type;

                $tmp['url'] = $user->profilePic->url ?? '/imgs/default_sm.png';

                $tmp['isfollower'] = 0;
                if(auth()->user()){
                    $logged_id = auth()->user()->id;
                    $follower_user_count = Follow::where('user_id', $logged_id)->where('follower_user_id', $user->id)->count();
                    if($follower_user_count != 0)
                        $tmp['isfollower'] = 1;
                }
                $tmp['isportal'] = $user->type == 'user' ? 0 : 1;
                array_push($followings, $tmp);
            }
        }
        $result = [];
        $result['followers'] = $followers;
        $result['followings'] = $followings;
        $result['auth_user'] = auth()->user();
        return response()->json($result);
    }

    public function list(Request $request) {
        return response()->json(User::all());
    }

    public function destroy($id) {
        $user = User::find($id);

        $user->medias()->delete();
        $user->menus()->delete();
        $user->coupon()->delete();
        $user->notifications()->delete();
        $user->notification_filter()->delete();
        if($user->profilePic) {
            $user->profilePic()->delete();
        }
        Comment::where('user_id', $id)->delete();
        $user->delete();

        return response()->json('success');
    }

    public function activate(Request $request) {
        $user = User::find($request->get('id'));
        
        $new_status = $user->is_active ? 0 : 1;
        $user->is_active = $new_status;
        if($user->type == 'company') {
            $user->state_license = $request->get('state_license');
        }
        $user->save();
        if($user->is_active) {
            $user->medias()->update(['is_active' => 1]);
            if ($user->profilePic) {
                $user->profilePic->update(['is_active' => 1]);
            }

        } else {
            $user->medias()->update(['is_active' => 0]);
            if ($user->profilePic) {
                $user->profilePic->update(['is_active' => 0]);
            }
        }
        return response()->json($new_status);
    }

    function getUnreads(Request $request) {
        return response()->json(auth()->user()->unread_message_user_count);
    }

    public function visited($id) {
        $user = User::find($id);
        $user->is_visited = 1;
        $user->save();
        return response()->json('success');
    }

    public function copyusername() {
        $users = User::where('type', 'user')->get();
        foreach ($users as $item) {
            $item->update([
                'username' => $item->name,
            ]);
        }
        dd('ok');
    }

}
