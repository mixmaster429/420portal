<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Generic;
use App\Models\Like;
use App\Models\Media;
use App\Models\Save;
use App\Models\Strain;
use App\Models\Menu;
use App\Models\Follow;
use App\User;
use App\Models\Portal;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StrainController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    // $strains = Strain::all();

    // $modalData = Generic::where('type', 'strain-modal-description')->first();

    // return view('strain.index', [
    //     'modalData' => $modalData,
    // ]);
    $strains = Strain::all();
    return response()->json($strains);
  }

  public function getModalData(Request $request)
  {
    $modalData = Generic::where('type', 'strain-modal-description')->first();
    return response()->json($modalData);
  }

  public function api()
  {
    $strains = Strain::all();

    return response()->json($strains);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $this->middleware('auth');

    $validateRequest = $this->validate($request, [
      'name' => 'required',
      'slug' => 'required|unique:strains',
      'description' => 'required',
      'category_id' => 'min:1|max:3',
    ]);

    // return response()->json($request->all());
    $strain = Strain::create($validateRequest);

    return response()->json($strain);
  }

  public function show($strain)
  {
    $category_array = ['indica', 'sativa', 'hybrid'];
    if (in_array($strain, $category_array)) {
      $category = Category::where('slug', $strain)->firstOrFail();
      return response()->json([
        'status' => 200,
        'type' => 'category',
        // 'strains' => $category->strains,
        'category' => $category,
      ]);
    } else {
      $strainDetail = Strain::where('slug', $strain)->firstOrFail();
      $brand_array = User::whereType('brand')->pluck('id');

      $strain_menus = $strainDetail->menus->whereNotIn('portal_id', $brand_array)->pluck('media_id')->unique()->toArray();
      $strain_menus = array_filter($strain_menus);

      $menus = $strainDetail->menus->whereNotIn('portal_id', $brand_array)->all()->toArray();

      $taggedMedia = Media::with('strain')->where('tagged_strain', $strainDetail->id)->orderByDesc('id')->get();
      // check private
      if (auth()->id()) {
        $follow_users = Follow::where('user_id', auth()->id())->distinct()->pluck('follower_user_id')->toArray();
        $private_users = User::where('is_private', 1)->whereNotIn('id', $follow_users)->pluck('id')->toArray();
      } else {
        $private_users = User::where('is_private', 1)->pluck('id')->toArray();
      }

      $posts_count = Media::with('strain', 'menu')
        ->where('is_active', 1)->whereNotIn('user_id', $private_users)
        ->where(function ($query) use ($strainDetail, $strain_menus) {
          return $query->where('tagged_strain', $strainDetail->id)
            ->orWhereIn('id', $strain_menus);
        })->count();
      $followers_count = Follow::where('follower_strain_id', $strainDetail->id)->count();
      $is_follower = Follow::where('user_id', auth()->id())->where('follower_strain_id', $strainDetail->id)->count();

      return response()->json([
        'type' => 'strain',
        'strain' => $strainDetail->load('category'),
        'menus' => $menus,
        'taggedMedia' => $taggedMedia,
        'posts_count' => $posts_count,
        'followers_count' => $followers_count,
        'is_follower' => $is_follower,
        'main_media' => $strainDetail->get_main_media(),
      ]);
    }
  }

  public function show_mobile($id)
  {
    $strain = Strain::with('category')->withCount(['likes', 'comments'])->find($id);
    $brand_array = User::whereType('brand')->pluck('id');
    $strain_menus = $strain->menus->whereNotIn('portal_id', $brand_array)->pluck('media_id')->unique()->toArray();
    $menus = $strain->menus->whereNotIn('portal_id', $brand_array)->all()->toArray();
    $strain->is_like = $strain->is_like();
    // check private
    $follow_users = Follow::where('user_id', auth()->id())->distinct()->pluck('follower_user_id')->toArray();
    $private_users = User::where('is_private', 1)->whereNotIn('id', $follow_users)->pluck('id')->toArray();


    $posts_count = Media::with('strain', 'menu')
      ->where('is_active', 1)->whereNotIn('user_id', $private_users)
      ->where(function ($query) use ($strain, $strain_menus) {
        return $query->where('tagged_strain', $strain->id)
          ->orWhereIn('id', $strain_menus);
      })->count();
    $followers_count = Follow::where('follower_strain_id', $strain->id)->count();
    $is_follower = Follow::where('user_id', auth()->id())->where('follower_strain_id', $strain->id)->count();
    $data = [
      'strain' => $strain,
      'menus' => $menus,
      'posts_count' => $posts_count,
      'followers_count' => $followers_count,
      'is_follower' => $is_follower,
      'main_media' => $strain->get_main_media(),
    ];
    return response()->json($data);
  }

  public function getMedia($strain)
  {
    $strainDetail = Strain::where('slug', $strain)->firstOrFail();
    $strain_menus = $strainDetail->menus->pluck('media_id')->unique()->toArray();
    $strain_menus = array_filter($strain_menus);
    // check private
    if (auth()->check()) {
      $follow_users = Follow::where('user_id', auth()->id())->distinct()->pluck('follower_user_id')->toArray();
      $private_users = User::where('is_private', 1)->whereNotIn('id', $follow_users)->pluck('id')->toArray();
    } else {
      $private_users = User::where('is_private', 1)->pluck('id')->toArray();
    }

    $taggedMedia = Media::with('strain', 'menu')
      ->where('is_active', 1)->whereNotIn('user_id', $private_users)
      ->where(function ($query) use ($strainDetail, $strain_menus) {
        return $query->where('tagged_strain', $strainDetail->id)
          ->orWhereIn('id', $strain_menus);
      })
      ->orderByDesc('created_at')->paginate(18);

    foreach ($taggedMedia as $key => $item) {
      $auth_id = auth()->id();
      auth()->id() && $item['loged_user'] = 1;
      $item->user->medias;
      $item->user->profilePic;

      $user_id = $item->user->id;

      $item['tagged_usersData'] = $item->taggedUsers;
      $item->taggedStrain;
      $item->taggedPortal;

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
      if (Follow::where('user_id', $user_id)->where('follower_user_id', $item['user_id'])->count() == 0)
        $item['isfollower'] = 0;
      else
        $item['isfollower'] = 1;
    }

    $return['postdata'] = $taggedMedia;

    return response()->json($return);
  }

  public function follow(Request $request)
  {
    $user_id = $request->get('user_id');
    $follower_id = $request->get('follower_id');
    $follow = Follow::where('user_id', $user_id)->where('follower_strain_id', $follower_id)->first();
    $is_follower = 0;
    if (!$follow) {
      Follow::create([
        'user_id' => $user_id,
        'follower_strain_id' => $follower_id
      ]);
      $is_follower = 1;
    } else {
      Follow::where('user_id', $user_id)->where('follower_strain_id', $follower_id)->delete();
    }
    $count = Follow::where('follower_strain_id', $follower_id)->count();
    $data = [
      'status' => 200,
      'count' => $count,
      'is_follower' => $is_follower,
    ];
    return response()->json($data);
  }

  public function getAllFollows(Request $request)
  {
    $id = $request->get('id');
    $follower_user_ids = Follow::where('follower_strain_id', $id)->distinct()->pluck('user_id');

    $followers = [];
    foreach ($follower_user_ids as $id) {
      $tmp = [];
      $user = User::find($id);
      $tmp['id'] = $user->id;
      $tmp['name'] = $user->name;
      $tmp['is_private'] = $user->is_private;
      if ($user->profilePic)
        $tmp['url'] = $user->profilePic->url;
      else
        $tmp['url'] = '/imgs/default.png';

      $tmp['is_follower'] = 0;
      if (auth()->user()) {
        $logged_id = auth()->id();
        $follower_user_count = Follow::where('user_id', $logged_id)->where('follower_user_id', $user->id)->count();
        if ($follower_user_count != 0) {
          $tmp['is_follower'] = 1;
        } else {
          if ($user->is_private) {
            $follow_requested = Notification::where('user_id', $id)->where('notifier_id', $logged_id)->whereType('follow_request')->exists();
            if ($follow_requested) {
              $tmp['is_follower'] = 2;
            }
          }
        }
      }
      array_push($followers, $tmp);
    }
    return response()->json(['followers' => $followers]);
  }

  public function strainCategory($type)
  {
    $category = Category::where('slug', $type)->firstOrFail();

    return view('strain.category', [
      'strains' => $category->strains,
      'category' => $category,
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function edit(Strain $strain)
  {
  }

  public function update(Request $request, $id)
  {

    $strain = Strain::findOrFail($id);
    if ($request->name != '') {
      $strain->name = $request->name;
    }

    if ($request->category_id != '') {
      $strain->category_id = $request->category_id;
    }

    if ($request->slug != '') {
      $strain->slug = $request->slug;
    }

    if ($request->description != '') {
      $strain->description = $request->description;
    }

    $strain->save();

    return response()->json($strain);
  }

  public function updateModal(Request $request, $id)
  {

    $validateRequest = $this->validate($request, [
      'description' => 'required',
    ]);

    $modalData = Generic::findOrFail($id);

    $modalData->description = $request->description;
    $modalData->save();

    return response()->json($modalData);
  }

  public function destroy($id)
  {
    Strain::destroy($id);
    return response()->json(['status' => 200]);
  }

  public function like(Request $request)
  {
    $strain = Strain::find($request->get('id'));
    $auth_like = $strain->likes()->where('user_id', Auth::id())->first();
    if ($auth_like) {
      $auth_like->delete();
      $is_like = 0;
    } else {
      Like::create([
        'user_id' => Auth::id(),
        'target_id' => $strain->id,
        'model' => 'strain',
      ]);
      $is_like = 1;
    }
    $data = [
      'status' => 200,
      'data' => [
        'count' => $strain->likes()->count(),
        'is_like' => $is_like,
      ],
    ];
    return response()->json($data);
  }

  public function update_slug()
  {
    ini_set('max_execution_time', 99999999999);
    $strains = Strain::all();
    foreach ($strains as $item) {
      $item->slug = convertNameToSlug($item->name);
      $item->save();
    }
    dump('success');
  }

  public function getMenus(Request $request)
  {
    $id = $request->id;
    $category = $request->category;
    $latitude = $request->latitude;
    $longitude = $request->longitude;
    switch ($category) {
      case 'flowers':
        $menus = Menu::where('category_id', '<=', 3)->where('strain_id', $id)->where('is_active', 1)->get();
        break;
      case 'concentrates':
        $menus = Menu::where('category_id', 4)->where('strain_id', $id)->where('is_active', 1)->get();
        break;
      case 'vape-pens':
        $menus = Menu::where('category_id', 5)->where('strain_id', $id)->where('is_active', 1)->get();
        break;
      case 'clones':
        $menus = Menu::where('category_id', 9)->where('strain_id', $id)->where('is_active', 1)->get();
        break;
      case 'seeds':
        $menus = Menu::where('category_id', 10)->where('strain_id', $id)->where('is_active', 1)->get();
        break;
      case 'pre-roll':
        $menus = Menu::where('category_id', 12)->where('strain_id', $id)->where('is_active', 1)->get();
        break;
      default:
        # code...
        break;
    }
    foreach ($menus as $item) {
      $item->distance = $item->portal->get_distance($latitude, $longitude);
      $item->shop_status = $item->portal->get_shop_status();
    }
    $brand_array = User::whereType('brand')->pluck('id');
    $data = $menus->whereNotIn('portal_id', $brand_array)->sortBy('distance')->values()->all();
    return response()->json($data);
  }
}
