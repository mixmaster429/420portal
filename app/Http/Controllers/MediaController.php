<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\Portal;
use App\Models\Like;
use App\Models\Media;
use App\Models\Save;
use App\User;
use App\Models\Strain;
use App\Models\Menu;
use App\Models\MediaUser;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Image;
use Carbon\Carbon;
use FFMpeg;

class MediaController extends Controller
{
    public function allpost(Request $request) {
        $user_id = auth()->id();
        $user_role = Auth::user()->role_id ?? 2;

        $defaultpost = Media::with('user')->find(1);
        if ($user_id == $defaultpost['user_id']) {
            $defaultpost['loged_user'] = 1;
        } else {
            $defaultpost['loged_user'] = 0;
        }

        $defaultpost['tagged_usersData'] = $defaultpost->taggedUsers;
        $defaultpost['is_flag'] = $defaultpost->is_flag();

        if ($defaultpost->taggedStrain) {
            $defaultpost['tagged_strainData'] = [$defaultpost->taggedStrain];
        } else {
            $defaultpost['tagged_strainData'] = [];
        }
        if ($defaultpost->taggedPortal) {
            $defaultpost['tagged_companyData'] = [$defaultpost->taggedPortal];
        } else {
            $defaultpost['tagged_companyData'] = [];
        }

        $defaultpost->user->medias;
        $defaultpost->user->profilePic;

        $media_id = $defaultpost['id'];
        $likes = Like::where('target_id', $media_id)->where('model', 'post')->count();
        $defaultpost['likes'] = $likes;
        $user_liked = Like::where('user_id', $user_id)->where('target_id', $media_id)->where('model', 'post')->count();
        $defaultpost['user_liked'] = $user_liked;
        $user_saved = Save::where('user_id', $user_id)->where('media_id', $media_id)->count();
        $defaultpost['user_saved'] = $user_saved;
        $defaultpost['user_role'] = $user_role;
        $count_comment = $defaultpost->comments()->count();
        $defaultpost['count_comment'] = $count_comment;

        $mod = new Media();
        $mod = $mod->with('user', 'menu')->where('is_active', 1)->where('id', '>', 1);
        // check private
        if(auth()->id()) {
            $follow_users = Follow::where('user_id', $user_id)->distinct()->pluck('follower_user_id')->toArray();
            $private_users = User::where('is_private', 1)->whereNotIn('id', $follow_users)->pluck('id')->toArray();
        } else {
            $private_users = User::where('is_private', 1)->pluck('id')->toArray();
        }

        $mod = $mod->whereNotIn('user_id', $private_users);

        if($request->get('keyword')){
            $keyword = $request->get('keyword');
            $user_id_array = User::where('name', 'like', "%$keyword%")->pluck('id')->toArray();
            if(count($user_id_array)) {
                $mod = $mod->whereIn('user_id', $user_id_array)->whereNull('portal_id');
            }

            $mod = $mod->where(function($query) use($keyword) {
                // Search by filename
                $key_slug = get_filename($keyword);
                $query = $query->orWhere('url', 'like', "%$key_slug%");

                //Search by description
                $query = $query->orWhere('description', 'like', "%$keyword%");
                // Search by username
                $user_media_array = User::where('name', 'like', "%$keyword%")->pluck('media_id');                
                $query = $query->orWhereIn('id', $user_media_array);                

                // //Search by Strain Name, media description
                $strain_array = Strain::where('name', 'like', "%$keyword%")->pluck('id');
                $menu_media_array = Menu::whereIn('strain_id', $strain_array)->orWhere('item_name', 'like', "%$keyword%")->orWhere('description', 'like', "%$keyword%")->pluck('media_id')->unique()->toArray();
                $query = $query->orWhereIn('id', $menu_media_array);

                return $query;
            });
        }

        
        if(auth()->id()) {
            if($request->get('bookmark') == true) {
                $bookmark_array = Save::where('user_id', $user_id)->pluck('media_id')->toArray();
                $mod->whereIn('id', $bookmark_array);
            }

            if($request->get('following') == true) {
                $following_users = Follow::where('user_id', $user_id)->whereNotNull('follower_user_id')->pluck('follower_user_id');               
                $following_strains = Follow::where('user_id', $user_id)->whereNotNull('follower_strain_id')->pluck('follower_strain_id');

                $media_user_ids = Media::whereIn('user_id', $following_users)->pluck('id');
                $media_strains_ids = Media::whereIn('tagged_strain', $following_strains)->pluck('id');
                $media_menus_ids = Menu::whereIn('strain_id', $following_strains)->pluck('media_id');
                $following_array = $media_user_ids->concat($media_strains_ids)->concat($media_menus_ids);
                $mod->whereIn('id', $following_array);
            }
        }

        if($request->get('account_type') && count($request->get('account_type')) != 0) {
            $account_type_array = $request->get('account_type');
            $latitude = $request->get('latitude');
            $longitude = $request->get('longitude');
            $distance = $request->get('distance');
            $mod = $mod->where(function($type_mod) use($account_type_array, $latitude, $longitude, $distance) {
                if(in_array('user', $account_type_array)) {
                    $user_media_array = User::pluck('media_id')->toArray();
                    $media_user_array = MediaUser::pluck('media_id')->toArray();
                    $type_mod = $type_mod->whereIn('id', array_merge($user_media_array, $media_user_array));
                }

                if(in_array('brand', $account_type_array)) {
                    $brand_ids = User::whereType('brand')->pluck('id')->toArray();
                    $type_mod = $type_mod->orWhere(function($query) use($brand_ids) {
                        return $query->whereIn('user_id', $brand_ids)->orWhereIn('tagged_portal', $brand_ids);
                    });
                }

                if(in_array('storefront', $account_type_array) || in_array('delivery', $account_type_array)) {
                    $store_type_array = [3];
                    if(in_array('storefront', $account_type_array)) {array_push($store_type_array, 1);}
                    if(in_array('delivery', $account_type_array)) {array_push($store_type_array, 2);}
                    $portals = User::whereType('company')->whereIn('store_type', $store_type_array)->get();
                    if($distance != '') {
                        foreach ($portals as $item) {
                            $item->distance = $item->get_distance($latitude, $longitude);
                        }
                        $portals = $portals->where('distance', '<', $distance);
                    }
                    $portal_ids = $portals->pluck('id')->toArray();
                    $type_mod = $type_mod->orWhere(function($query) use($portal_ids) {
                                return $query->whereIn('user_id', $portal_ids)->orWhereIn('tagged_portal', $portal_ids);
                            });
                }
                return $type_mod;
            });
        }

        // $allposts = $mod->orderBy('created_at', 'desc')->get()->except(1);
        $per_page = 18;
        if($request->get('per_page') != '') {
            $per_page = $request->get('per_page');
        }
        $allposts = $mod->orderBy('created_at', 'desc')->paginate($per_page);

        foreach ($allposts as $item) {
            if ($user_id == $item['user_id']) {
                $item['loged_user'] = 1;
            } else {
                $item['loged_user'] = 0;
            }
            // $item->user->media;
            
            $item->user->profilePic ?? '';

            $item['tagged_usersData'] = $item->taggedUsers;
            $item->taggedStrain;
            $item->taggedPortal;
            $item->is_flag = $item->is_flag();
            // $item->menu;

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
            $likes = Like::where('target_id', $media_id)->count();
            $item['likes'] = $likes;
            $user_liked = Like::where('user_id', $user_id)->where('target_id', $media_id)->where('model', 'post')->count();
            $item['user_liked'] = $user_liked;
            $user_saved = Save::where('user_id', $user_id)->where('media_id', $media_id)->count();
            $item['user_saved'] = $user_saved;
            $item['user_role'] = $user_role;
            if($item['portal']){
                if(Follow::where('user_id', $user_id)->where('follower_company_id', $item['portal']['id'])->count() == 0)
                    $item['isfollower'] = 0;
                else
                    $item['isfollower'] = 1;
            }else{
                if(Follow::where('user_id', $user_id)->where('follower_user_id', $item['user_id'])->count() == 0)
                    $item['isfollower'] = 0;
                else
                    $item['isfollower'] = 1;
            }

            $count_comment = $item->comments()->count();
            $item['count_comment'] = $count_comment;
            $item['description_expanded'] = false;
        }

        $return = [];
        $return['default'] = $defaultpost;
        $return['allposts'] = $allposts;

        return response()->json($return);
    }

    public function removetag(Request $request) {
      $type = $request->get('type');
      $media_id = $request->get('media_id');

      if($type === "user") {
        MediaUser::where('media_id', $media_id)->where('user_id', Auth::user()->id)->delete();
      } else {
        $media = Media::find($media_id);
        $media->tagged_portal = null;
        $media->save();
      }

      return response()->json(['success' => 'success']);
    }

    public function gettaged(Request $request)
    {
      $item = Media::find($request->get('id'));

      $item['tagged_usersData'] = $item->taggedUsers;

      foreach ($item['tagged_usersData'] as $key => $user) 
      {
          if(Auth::check()) {
            $user_id = Auth::user()->id;
            $follower_user_id = $user->id;
            $follower = User::find($follower_user_id);
            $count = Follow::where('user_id', $user_id)->where('follower_user_id', $follower_user_id)->count();
            $following = 0;
            if($count == 0) {
                if($follower->is_private) {
                    $follow_requested = Notification::where('user_id', $follower_user_id)->where('notifier_id', $user_id)->whereType('follow_request')->exists();
                    if($follow_requested) {
                      $following = 2;
                    }
                }
            } else {
              $following = 1;
            }            
            $user['following'] = $following;
          } else {
            $user['following'] = 0;
          }
      }

      if ($item->taggedStrain) {
          $item->taggedStrain['main_media'] = $item->taggedStrain->get_main_media();
          $is_follower = Follow::where('user_id', auth()->id())->where('follower_strain_id', $item->taggedStrain->id)->count();
          $item->taggedStrain['following'] = $is_follower;
          $item['tagged_strainData'] = [$item->taggedStrain];
      } else {
          $item['tagged_strainData'] = [];
      }
      if ($item->taggedPortal) {
          $following = 0;
          if(Auth::check()) {
            $user_id = Auth::user()->id;
            $follower_user_id = $item->taggedPortal->id;
            $follower = User::find($follower_user_id);
            $count = Follow::where('user_id', $user_id)->where('follower_user_id', $follower_user_id)->count();
            
            if($count == 0) {
                if($follower->is_private) {
                    $follow_requested = Notification::where('user_id', $follower_user_id)->where('notifier_id', $user_id)->whereType('follow_request')->exists();
                    if($follow_requested) {
                      $following = 2;
                    }
                }
            } else {
              $following = 1;
            }
          }
          $item->taggedPortal['following'] = $following;
          $item['tagged_companyData'] = [$item->taggedPortal];
      } else {
          $item['tagged_companyData'] = [];
      }

      return response()->json($item);
    }

    public function mediauplaod(Request $request)
    {        
        $file = $request->file('postfile');

        $mime = $file->getMimeType();

        if (strstr($mime, 'video')) {
            $file_type = 'video';
            $destinationPath = 'uploaded/video';
        } elseif (strstr($mime, 'image')) {
            $file_type = 'image';
            $destinationPath = 'uploaded/image';
            $ImageUpload = Image::make($file)->resize(600, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->orientate();
        }

        $ext = $file->getClientOriginalExtension();

        $filename = 'm'.time().'.'.$ext;

        if ($request->get('media_type') == 'user') {
            $filename = get_filename($request->get('username'))."_marijuana_".time().'.'.$ext;
        }

        if ('video' === $file_type) {
            $file->move($destinationPath, $filename);
        } elseif ('image' === $file_type) {
            $ImageUpload->save($destinationPath.'/'.$filename, 85);
        }

        $return = [];
        $return['fileurl'] = '/'.$destinationPath.'/'.$filename;
        $return['filetype'] = $file_type;

        return response()->json($return);
    }

    public function store(Request $request) {
        $file = $request->file('file');
        $file_type = $request->type;

        if ('video' === $file_type) {
            $destinationPath = 'uploaded/video';
        } elseif ('image' === $file_type) {
            $destinationPath = 'uploaded/image';
            $ImageUpload = Image::make($file)->resize(600, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->orientate();
        }

        $ext = $file->getClientOriginalExtension();


        $strain = Strain::find($request->get('taggedStrains'));
        if($strain) {
            $filename = get_filename($strain->name)."_marijuana_".time().'.'.$ext;
        } else {
            $filename = 'm-'.auth()->user()->name.'-'.auth()->id().'-'.time().'.'.$ext;
        }

        if ('video' === $file_type) {
            $file->move($destinationPath, $filename);
        } elseif ('image' === $file_type) {
            $ImageUpload->save($destinationPath.'/'.$filename, 85);
        }

        $media = new Media();

        $media->url = '/'.$destinationPath.'/'.$filename;
        $media->type = $file_type;
        $media->description = $request->description;
        $media->model = $request->model;
        if ($request->portal_id) {
            $media->portal_id = $request->portal_id;
        }
        $media->user_id = Auth::id();
        // if($request->taggedCompanies)
        $media->tagged_strain = $request->taggedStrains;
        $media->tagged_portal = $request->taggedCompanies;

        $media->is_active = 1;

        $media->save();
        $media->user->update([
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        if($media->type == 'video') {
            $media->convert_video();
        }
        $media->taggedUsers()->attach(json_decode($request->taggedUsers));

        return response()->json($media);
    }

    public function update(Request $request, Media $media)
    {
        // return response()->json($request);
        $this->middleware('auth');

        $media->description = $request->description;
        $media->tagged_strain = $request->taggedStrains;
        $media->tagged_portal = $request->taggedCompanies;

        $media->save();
        $media->user->update([
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $media->taggedUsers()->sync(json_decode($request->taggedUsers));

        return response()->json($media);
    }

    public function adduplaod(Request $request)
    {
        $media = new Media();

        $media->url = $request->url;
        $media->type = $request->type;
        $media->description = $request->description;
        $media->model = $request->model;
        $media->user_id = Auth::id();

        $media->save();

        return response()->json('success');
    }

    public function destroy(Media $media)
    {
        $this->middleware('auth');
        
        $tempuser = $media->user;
        if($tempuser && $tempuser->media_id == $media->id) {
            $tempuser->update(['media_id' => '']);
        }
        MediaUser::where('media_id', $media->id)->delete();
        $media->delete();

        return response()->json($media);
    }

    public function activate() {
        Media::where('id', '!=', 0)->update(['is_active' => 1]);
    }

    public function show(Request $request, $id) {
        $media = Media::with(['user'])->find($id);
        $user_id = auth()->id();
        $user_role = auth()->user()->role_id ?? 2;
        if ($user_id == $media['user_id']) {
            $media['loged_user'] = 1;
        } else {
            $media['loged_user'] = 0;
        }
        // $item->user->media;
        $media->user->profilePic;

        $media['tagged_usersData'] = $media->taggedUsers;
        $media->taggedStrain;
        $media->taggedPortal;
        $media->menu;

        if ($media->taggedStrain) {
            $media['tagged_strainData'] = [$media->taggedStrain];
        } else {
            $media['tagged_strainData'] = [];
        }
        if ($media->taggedPortal) {
            $media['tagged_companyData'] = [$media->taggedPortal];
        } else {
            $media['tagged_companyData'] = [];
        }

        $media_id = $media['id'];
        $likes = Like::where('target_id', $media_id)->where('model', 'post')->count();
        $media['likes'] = $likes;
        $user_liked = Like::where('user_id', $user_id)->where('target_id', $media_id)->where('model', 'post')->count();
        $media['user_liked'] = $user_liked;
        $user_saved = Save::where('user_id', $user_id)->where('media_id', $media_id)->count();
        $media['user_saved'] = $user_saved;
        $media['user_role'] = $user_role;
        
        if(Follow::where('user_id', $user_id)->where('follower_user_id', $media['user_id'])->count() == 0)
            $media['isfollower'] = 0;
        else
            $media['isfollower'] = 1;

        $count_comment = $media->comments()->count();
        $media['count_comment'] = $count_comment;

        $username = $media->user->name ?? '';
        if($media->portal) {
            $username = $media->portal->name;
        }
        $meta_title = $username;
        $meta_keywords = $username;
        $meta_description= $media->description;
        if($media->portal && $media->menu) {
            $meta_title = $media->portal->name . " - " . $media->menu->item_name . " - " . $media->menu->brand;
            $meta_keywords = $media->portal->name . ", " . $media->menu->item_name . ", " . $media->menu->brand;
            $meta_description = $media->portal->name . " - " . $media->menu->item_name . " - " . $media->menu->brand . " - " . $media->description;
        }
        $media['meta'] = [
            'title' => $meta_title,
            'keywords' => $meta_keywords,
            'description' => $meta_description,
        ];
        
        return response()->json($media);      
    }

    public function api_add(Request $request) {
        $file = $request->file('postfile');
        $mime = $file->getMimeType();
        
        if (strstr($mime, 'video')) {
            $file_type = 'video';
            $destinationPath = 'uploaded/video';
        } elseif (strstr($mime, 'image')) {
            $file_type = 'image';
            $destinationPath = 'uploaded/image';
            $ImageUpload = Image::make($file)->resize(600, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->orientate();
        }

        $ext = $file->getClientOriginalExtension();

        $fileTime = time();
        $filename = 'm'.$fileTime.'.'.$ext;

        if ($request->get('media_type') == 'user') {
            $filename = get_filename($request->get('username'))."_marijuana_".time().'.'.$ext;
        }

        if ('video' === $file_type) {
            $file->move($destinationPath, $filename);
            $videoFilePath = $destinationPath .'/'. $filename;
            $thumbnailFile = 'uploaded/image/m'.$fileTime.'.jpg';
            FFMpeg::fromDisk('video_root')
            ->open($videoFilePath)
            ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(0.2))
            ->save($thumbnailFile);
        } elseif ('image' === $file_type) {
            $ImageUpload->save($destinationPath.'/'.$filename, 85);
        }

        $return = [];
        $return['fileurl'] = '/'.$destinationPath.'/'.$filename;
        $return['filetype'] = $file_type;

        return response()->json($return);

    }

    public function api_store(Request $request) {
        $media = new Media();
        $file_type = $request->get('type');
        $media->url = $request->get('media_url');
        $media->type = $file_type;
        $media->description = $request->get('description');
        $media->model = $request->get('model');
        $media->user_id = auth()->id();
        $media->tagged_strain = $request->get('taggedStrains');
        $media->tagged_portal = $request->get('taggedCompanies');

        $media->is_active = 1;

        $media->save();
        $media->taggedUsers()->attach(json_decode($request->get('taggedUsers'), true));

        if($media->type == 'video') {
            try {
                $media->createThumbnail();
            } catch (\Throwable $th) {
                //throw $th;
            }            
        }
        $media->user->update([
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return response()->json(['status' => 200, 'data' => $media]);
    }

    public function api_update(Request $request) {
        $media = Media::find($request->get('id'));
        $media->description = $request->get('description');
        $media->tagged_strain = $request->get('taggedStrains');
        $media->tagged_portal = $request->get('taggedCompanies');
        // if($media->url != $request->get('media_url') || $media->type != $request->get('type')) {            
        //     $media->url = $request->get('media_url');
        //     $media->type = $request->get('type');
        // }

        $media->save();
        $media->taggedUsers()->sync(json_decode($request->get('taggedUsers'), true));
        $media->user->update([
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return response()->json(['status' => 200, 'data' => $media]);
    }

    public function get_thumbnail($id) {
        $media = Media::find($id);
        $media->createThumbnail();
        dd('ok');
    }
}