<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Generic;
use App\Models\Portal;
use App\Models\Menu;
use App\Models\Follow;
use App\Models\Media;
use App\User;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Save;

use Auth;
use Mail;
use App\Mail\PortalRegisterMail;

use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;
use JWTAuth;

class BrandController extends Controller
{
    public function show() {
        return view('brand.show');
    }

    public function appGetBrand(Request $request) {
        $brand = Portal::find($request->get('id'));
        if($brand->is_active != 1) {
            return response()->json(['success' => false, 'message' => 'Inactive Brand']);
        }
        // check private
        if(auth()->id()) {
            $brand->is_follower = Follow::where('user_id', auth()->id())->where('follower_company_id', $brand->id)->exists();
            $follow_users = Follow::where('user_id', auth()->id())->distinct()->pluck('follower_user_id')->toArray();
            $private_users = User::where('is_private', 1)->whereNotIn('id', $follow_users)->pluck('id')->toArray();
        } else {
            $brand->is_follower = false;
            $private_users = User::where('is_private', 1)->pluck('id')->toArray();
        }

        $brand_menu_media_array = $brand->menus()->where('is_active', 1)->pluck('media_id')->toArray();
        if($brand->media) {
            array_push($brand_menu_media_array, $brand->media_id);
        }

        $brand->posts_count = Media::where('is_active', 1)->whereNotIn('user_id', $private_users)
                ->whereIn('id', $brand_menu_media_array)->count();        

        $brand->comments_count = Comment::where('target_id', $brand->id)->where('target_model', 'portal')->count();
        $brand->load('menus');
        return response()->json(['success' => true, 'data' => $brand]);
    }

    public function create(Request $request) {
        if(auth()->check()) {
            auth()->logout();
        }
        return view('brand.add');
    }

    public function save(Request $request) {
        $item = new User();
        $item->name = $request->get('name');
        $item->username = $request->get('username');
        $item->email = $request->get('email');
        $item->password = bcrypt($request->get('password'));
        $item->type = 'brand';
        $item->slug = convertNameToSlug($request->get('name'));
        $item->website_url = $request->get('website_url');
        $item->facebook_url = $request->get('facebook_url');
        $item->twitter_url = $request->get('twitter_url');
        $item->instagram_url = $request->get('instagram_url');
        $item->youtube_url = $request->get('youtube_url');
        $item->description = $request->get('description');        
        $item->save();
        if($request->get('image_url') != '') {
            $media = new Media();
            $media->url = $request->get('image_url');
            $media->type = 'image';
            $media->model = 'logo';
            $media->description = $request->get('description');
            $media->user_id = $item->id;
            $media->save();
            $item->media_id = $media->id;
            $item->save();
        }
        if(!$request->has('is_mobile')) {
            Auth::login($item);
        }        
        return response()->json(['status' => 200, 'brand' => $item]);
    }

    public function getBrands(Request $request) {
        $user = auth()->user();
        $mod = new User();
        $mod = $mod->whereType('brand');
        if ($user && $user->role_id == 1) {
            $mod = $mod->where('is_active', 1);
        }
        $brands = $mod->orderBy('name')->paginate(30);
        return response()->json(['status' => 200, 'brands' => $brands]);
    }

    public function update(Request $request) {
        $item = User::find($request->get('id'));
        $item->name = $request->get('name');
        $item->username = $request->get('username');
        $item->email = $request->get('email');
        $item->slug = convertNameToSlug($request->get('name'));
        $item->website_url = $request->get('website_url');
        $item->facebook_url = $request->get('facebook_url');
        $item->twitter_url = $request->get('twitter_url');
        $item->instagram_url = $request->get('instagram_url');
        $item->youtube_url = $request->get('youtube_url');
        $item->description = $request->get('description');
        if($request->get('image_url') == '') {
            $item->media_id = null;
        } else {
            if(!$item->profilePic) {              
                $media = new Media();
                $media->url = $request->get('image_url');
                $media->type = 'image';
                $media->model = 'logo';
                $media->description = $request->get('description');
                $media->user_id = $item->id;
                $media->save();
                $item->media_id = $media->id;  
            } else if($request->get('image_url') != $item->profilePic->url) {
                $media = $item->profilePic;
                $media->url = $request->get('image_url');
                $media->save();
            }
        }
        $item->save();
        return response()->json(['status' => 200, 'brand' => $item]);
    }

    public function get_all_menus(Request $request) {
        $id = $request->get('id');
        $brand = User::find($id);
        if(Auth::id() == $brand->id || Auth::id() == 1) {
            $menus = Menu::where('portal_id', $id)->orderBy('category_id')->get();
        } else {
            $menus = Menu::where('portal_id', $id)->where('is_active', 1)->orderBy('category_id')->get();
        }

        $brand_categories_array = $menus->pluck('category_id')->unique()->toArray();
        $brand_categories = array();
        foreach ($brand_categories_array as $item) {
            array_push($brand_categories, Category::find($item));
        }

        $data = [
            'status' => 200,
            'data' => [
                'menus' => $menus,
                'brand_categories' => $brand_categories,
            ],
        ];

        return response()->json($data);
    }

    public function getCategoryMedia(Request $request){
        $category = $request->get('category');
        $brand_array = User::whereType('brand')->where('is_active', 1)->pluck('id');
        if($category == 'flowers') {
            $category_array = [1, 2, 3];
        } else {
            $category_array = Category::whereSlug($category)->pluck('id')->toArray();
        }
        
        $brand_menus = Menu::whereIn('category_id', $category_array)->whereIn('portal_id', $brand_array)->pluck('media_id')->toArray();
        
        // check private
        if(auth()->check()) {
            $follow_users = Follow::where('user_id', auth()->id())->distinct()->pluck('follower_user_id')->toArray();
            $private_users = User::where('is_private', 1)->whereNotIn('id', $follow_users)->pluck('id')->toArray();
        } else {
            $private_users = User::where('is_private', 1)->pluck('id')->toArray();
        }
        
        $allposts = Media::with('menu')
                        ->whereIn('id', $brand_menus)
                        ->where('is_active', 1)->whereNotIn('user_id', $private_users)
                        ->orderByDesc('created_at')->paginate(18);
        foreach ($allposts as $key => $item) {
            $auth_id = auth()->id();
            auth()->id() && $item['loged_user'] = 1;
            $item->user->media;
            $item->user->profilePic;

            $user_id = $item->user->id;

            $item['tagged_usersData'] = $item->taggedUsers;
            $item->taggedStrain;
            $item->taggedPortal;

            if ($item->portal_id) {
                $portal = $item->portal;
                if ($portal) {
                    $item->portal->media;
                }
            }

            $media_id = $item['id'];
            $likes = Like::where('target_id', $media_id)->where('model', 'post')->count();
            $item['likes'] = $likes;
            $item['count_comment'] = $item->comments()->count();
            $user_liked = Like::where('user_id', $user_id)->where('target_id', $media_id)->where('model', 'post')->count();
            $item['user_liked'] = $user_liked;

            $user_saved = Save::where('user_id', $user_id)->where('media_id', $media_id)->count();
            $item['user_saved'] = $user_saved;
            $item['description_expanded'] = false;

            // Added
            if(Follow::where('user_id', $user_id)->where('follower_user_id', $item['user_id'])->count() == 0)
                $item['isfollower'] = 0;
            else
                $item['isfollower'] = 1;
        }

        $return['postdata'] = $allposts;

        return response()->json($return);
    }

    public function getModalData(Request $request) {
        $modalData = Generic::where('type', 'brand-modal-description')->first();
        return response()->json($modalData);
    }

    public function updateModal(Request $request, $id) {

        $validateRequest = $this->validate($request, [
            'description' => 'required',
        ]);

        $modalData = Generic::findOrFail($id);

        $modalData->description = $request->description;
        $modalData->save();

        return response()->json($modalData);
    } 
}
