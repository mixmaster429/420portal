<?php

namespace App\Http\Controllers;

use App\Models\BlockPortal;
use App\Models\Companychat;
use App\Models\Coupon;
use App\Models\Media;
use App\Models\Portal;
use App\Models\Category;
use App\Models\Strain;
use App\Models\Menu;
use App\User;
use App\Models\State;
use App\Models\City;
use App\Models\Comment;
use App\Models\Notification;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Mail;
use App\Mail\PortalRegisterMail;
use App\Mail\StrainRequestMail;

use Carbon\Carbon;

class PortalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
    }

    public function create() {
        if(auth()->check()) {
            auth()->logout();
        }
        return view('Portal.addportal');
    }

    public function store(Request $request) {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:25', 'unique:users'],
            'password' => ['required', 'string'],
            'phone_number' => ['required', 'string'],
            'email' => ['required', 'email'],
            'address' => ['required', 'string'],
            'state_license' => ['required', 'string'],
        ]);

        $data = $request->all();

        if ('' != $data['address_name'] && null != $data['address_name']) {
            $data['address'] = $data['address_name'];
            unset($data['address_name']);
        } else {
            $address = $data['address'];
            $address = explode(', ', $address);

            $data['address'] = $address[0];
            $data['city'] = $address[1];
            if (!empty($address[2])) {
                $state = $address[2];
                $state = explode(' ', $state);

                if (count($state) >= 2) {
                    $data['state'] = $state[0];
                    $data['postal'] = $state[1] ? $state[1] : '';
                }
            }

            unset($data['address_name']);
        }
        $data['password'] = bcrypt($data['password']);
        $data['slug'] = str_slug($data['name']);

        unset($data['postfile']);

        $media = new Media();
        $media->url = $data['pa_logourl'];
        $media->type = $data['pa_mediatype'];

        unset($data['pa_logourl'], $data['pa_mediatype'], $data['old_condition'], $data['terms_condition']);

        $data['is_active'] = 1;
        $data['type'] = 'company';

        $portal = User::create($data);
        $portal_id = $portal->id;

        Mail::to($portal->email)->send(new PortalRegisterMail($portal));

        $media->model = 'logo';
        $media->description = $data['description'];
        $portal->save();

        $media->user_id = $portal->id;
        $media->save();
        $portal->media_id = $media->id;
        $portal->save();
        Auth::login($portal);
        $request->session()->flash('Portal-create', 'Portal-create');

        return redirect(route('profile', $portal->username));
    }

    public function searchPortals(Request $request) {
        $user_latitude = $request['lat'];
        $user_longitude = $request['lng'];

        $mod = new User();
        $mod = $mod->with('profilePic')->whereType('company')->where('is_active', 1);

        if ($request->get('portal_id') != '') {
            $portal_id = $request->get('portal_id');
            $mod = $mod->where('id', $portal_id);
        }

        $type_array = $request->get('business_type');
        array_push($type_array, 3);
        $mod = $mod->whereIn('store_type', $type_array);

        if(in_array('recreational', $request->get('filters')) && !in_array('medical', $request->get('medical'))) {
            $mod = $mod->where('recreational', 1);
        }
        if(in_array('medical', $request->get('filters')) && !in_array('recreational', $request->get('filters'))) {
            $mod = $mod->where('medical', 1);
        }
        $all_companies_array = User::whereType('company')->pluck('id');
        $menu_mod = new Menu();
        $menu_mod = $menu_mod->where('is_active', 1)->whereIn('portal_id', $all_companies_array);
        $flag_show_menu = 0;
        if($request->get('category') != '') {
            $flag_show_menu = 1;
            $category_id = $request->get('category');
            $flower = [1,2,3];
            if (in_array($category_id, $flower)) {
                $menu_mod = $menu_mod->whereIn('category_id', $flower);
            } else {
                $menu_mod = $menu_mod->where('category_id', $category_id);
            }

            if($request->get('menu_price_type') != '') {
                $price_type = $request->get('menu_price_type');
                $menu_mod = $menu_mod->whereNotNull($price_type);

                if($request->get('menu_price_max') != '') {
                    $menu_price_max = $request->get('menu_price_max');
                    $menu_mod = $menu_mod->where($price_type, '<=', $menu_price_max);
                }
            }
        }

        if($request->get('menu_strain') != '') {
            $flag_show_menu = 1;
            $strain_name = $request->get('menu_strain');
            $strain_array = Strain::where('name', 'like', "%$strain_name%")->pluck('id');
            // $menu_strain_brands = Portal::where('name', 'like', "%$strain_name%")->whereType('brand')->pluck('id')->toArray();
            $menu_mod = $menu_mod->where(function($query) use($strain_name, $strain_array) {
                    return $query->whereIn('strain_id', $strain_array)
                        ->orWhere('item_name', 'like', "%$strain_name%");
                        // ->orWhereIn('portal_id',  $menu_strain_brands);                
                });
        }

        $menus = $menu_mod->get();
        $menu_portal_array = $menus->pluck('portal_id')->unique()->toArray();
        if($flag_show_menu) {
            $portals = $mod->whereIn('id', $menu_portal_array)->get();
        } else {
            $portals = $mod->get();
        }

        foreach ($portals as $item) {
            $item->distance = $item->get_distance($user_latitude, $user_longitude);
            $item->shop_status = $item->get_shop_status();
            $item->menus = [];
            if($flag_show_menu) {                
                $portal_menus = $menus->where('portal_id', $item->id)->all();
                $portal_menus_array = [];
                foreach ($portal_menus as $m_item) {
                   array_push($portal_menus_array, $m_item);
                }
                $item->menus = $portal_menus_array;
            }
        }

        if(in_array('open_now', $request->get('filters'))) {
            $portals = $portals->where('shop_status', 2)->all();
            $portals = array_values((array) $portals);
        }

        $data = [
            'status' => 200,
            'portals' => $portals,
        ];

        return response()->json($data);

    }

    public function update(Request $request) {
        $this->middleware('auth');
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:25'],
            'phone_number' => ['required', 'string'],
            'address_name' => ['required', 'string'],
            'state_license' => ['required', 'string'],
        ]);

        $portal = User::find($request->get('id'));
        $data = $request->all();

        $oldMedia = $portal->media->url ?? null;
        $currentMedia = $request->pa_logourl;

        // dd($oldMedia, $currentMedia);

        if ($oldMedia !== $currentMedia) {
            // dd('new media');
            $media = new Media();

            $media->url = $data['pa_logourl'];
            $media->type = 'image';

            $media->user_id = $portal->id;
            $media->model = 'logo';
            $media->description = $data['description'];
            $media->save();
            $media_id = $media->id;
            $portal->media_id = $media_id;
        }

        if ('' != $data['address_name'] && null != $data['address_name']) {
            $data['address'] = $data['address_name'];
            unset($data['address_name']);
        } else {
            $address = $data['address'];
            $address = explode(', ', $address);

            $data['address'] = $address[0];
            $data['city'] = $address[1];
            if (!empty($address[2])) {
                $state = $address[2];
                $state = explode(' ', $state);

                if (count($state) >= 2) {
                    $data['state'] = $state[0];
                    $data['postal'] = $state[1] ? $state[1] : '';
                }
            }

            unset($data['address_name']);
        }

        $data['slug'] = str_slug($data['name']);
        unset($data['postfile'], $data['pa_logourl'], $data['pa_mediatype']);

        $portal_id = $portal->id;

        $portal->name = $data['name'];
        $portal->username = str_replace(' ', '', $data['username']);
        $portal->slug = $data['slug'];

        $portal->description = $data['description'];
        $portal->store_type = $data['store_type'];
        $portal->recreational = $data['recreational'] ?? null;
        $portal->medical = $data['medical'] ?? null;
        $portal->atm = $data['atm'] ?? null;
        $portal->security = $data['security'] ?? null;
        $portal->phone_number = $data['phone_number'];
        $portal->website_url = $data['website_url'];
        $portal->facebook_url = $data['facebook_url'];
        $portal->twitter_url = $data['twitter_url'];
        $portal->instagram_url = $data['instagram_url'];
        $portal->youtube_url = $data['youtube_url'];
        $portal->email = $data['email'];
        $portal->state_license = $data['state_license'];

        $portal->timezone = $data['timezone'] ?? null;
        $portal->latitude = $data['latitude'];
        $portal->longitude = $data['longitude'];

        $portal->address = $data['address'] ?? null;
        $portal->city = $data['city'] ?? null;
        $portal->state = $data['state'] ?? null;
        $portal->postal = $data['postal'] ?? null;
        $portal->suite = $data['suite'];

        $portal->mon_open = $data['mon_open'];
        $portal->mon_close = $data['mon_close'];
        $portal->mon_closed = $data['mon_closed'] ?? null;

        $portal->tue_open = $data['tue_open'];
        $portal->tue_close = $data['tue_close'];
        $portal->tue_closed = $data['tue_closed'] ?? null;

        $portal->wed_open = $data['wed_open'];
        $portal->wed_close = $data['wed_close'];
        $portal->wed_closed = $data['wed_closed'] ?? null;

        $portal->thu_open = $data['thu_open'];
        $portal->thu_close = $data['thu_close'];
        $portal->thu_closed = $data['thu_closed'] ?? null;

        $portal->fri_open = $data['fri_open'];
        $portal->fri_close = $data['fri_close'];
        $portal->fri_closed = $data['fri_closed'] ?? null;

        $portal->sat_open = $data['sat_open'];
        $portal->sat_close = $data['sat_close'];
        $portal->sat_closed = $data['sat_closed'] ?? null;

        $portal->sun_open = $data['sun_open'];
        $portal->sun_close = $data['sun_close'];
        $portal->sun_closed = $data['sun_closed'] ?? null;

        $portal->save();
        $request->session()->flash('Portal-update', 'Portal-update');
        
        return response()->json(['status' => 200, 'result' => $portal]);        
    }

    public function destroy($id) {
        $portal = User::find($id);
        if($portal->medias){
            $portal->medias()->delete();
        }
        if($portal->taggedMedia) {
            $portal->taggedMedia()->delete();
        }
        if($portal->taggedPortalMedia) {
            $portal->taggedPortalMedia()->delete();
        }
        if($portal->menus) {
            $portal->menus()->delete();
        }
        if($portal->coupon) {
            $portal->coupon()->delete();
        }

        Comment::where('target_id', $portal->id)->where('target_model', 'user')->delete();
        $portal->delete();
        return response()->json('success');
    }

    public function searchmap($state_slug = null, $city_slug = null)
    {
        $state = $city = '';
        if($state_slug) {
            $state_slug = str_replace('-', ' ', $state_slug);
            $state_slug = str_replace('_', ' ', $state_slug);
            $state = State::where('name', $state_slug)->firstOrFail();
        }
        if($city_slug) {
            $city_slug = str_replace('-', ' ', $city_slug);
            $city_slug = str_replace('_', ' ', $city_slug);
            $city = City::where('name', $city_slug)->firstOrFail();
        }
        return view('Portal.mappage', compact('state', 'city'));
    }

    public function list(Request $request) {
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $portals = User::with('coupon')->whereType('company')->get();
        
        foreach ($portals as $item) {
            if($item->type == 'company') {                
                $item->distance = $item->get_distance($latitude, $longitude);
                $item->shop_status = $item->get_shop_status();
            }
        }
        return response()->json($portals);
    }

    public function getAllPortals() {
        $portals = User::where('type', 'company')->get();
        return response()->json($portals);
    }

    // Customize Company Menu

    public function get_all_menus(Request $request) {
        $id = $request->get('id');
        $category_id = $request->get('category_id');
        $price_type = $request->get('price_type');
        $price_max = $request->get('price_max');
        $portal = User::find($id);
        $mod = new Menu();
        if(Auth::id() == $portal->id || Auth::id() == 1) {
            $mod = $mod->where('portal_id', $id);
        } else {
            $mod = $mod->where('portal_id', $id)->where('is_active', 1);
        }

        $menus = $mod->orderBy('category_id')->get();

        $portal_categories_array = $menus->pluck('category_id')->unique()->toArray();

        $portal_categories = array();
        foreach ($portal_categories_array as $item) {
            array_push($portal_categories, Category::find($item));
        }

        $data = [
            'status' => 200,
            'data' => [
                'menus' => $menus,
                'portal_categories' => $portal_categories,
            ],
        ];

        return response()->json($data);
    }

    public function create_menu(Request $request) {
        $item = new Menu();
        $item->portal_id = $request->get('portal_id');
        $item->category_id = $request->get('category');
        $item->strain_id = $request->get('strain'); 
        $item->item_name = $request->get('item_name');
        $item->price_gram = $request->get('price_gram');
        $item->price_half_gram = $request->get('price_half_gram');
        $item->price_eighth = $request->get('price_eighth');
        $item->price_quarter = $request->get('price_quarter');
        $item->price_half_oz = $request->get('price_half_oz');
        $item->price_oz = $request->get('price_oz');
        $item->price_each = $request->get('price_each');
        $item->description = $request->get('description');
        $item->is_active = 1;

        if($request->get('strain')) {
            $strain_id = $request->get('strain');
            $strain_name = Strain::find($strain_id)->name;
        } else {
            $strain_name = $request->get('item_name');
        }
        $file_name = get_filename($strain_name)."_marijuana_".time();

        if($request->has("file") && $request->file('file') != null){
            $picture = request()->file('file');
            $imageName = $file_name.'.'.$picture->getClientOriginalExtension();
            $mime = $picture->getMimeType();
            if(strstr($mime, 'video/')) {
                $file_type = 'video';
                $picture->move(public_path('uploaded/video/'), $imageName);
                $image_url = '/uploaded/video/'.$imageName;
            } elseif (strstr($mime, 'image/')) {
                $file_type = 'image';
                $picture->move(public_path('uploaded/image/'), $imageName);
                $image_url = '/uploaded/image/'.$imageName;
            }


            $media = Media::create([
                'url' => $image_url,
                'model' => 'menu',
                'type' => $file_type,
                'user_id' => $request->get('portal_id'),
                'description' => $request->get('description'),
            ]);

            if($media->type == 'video') {
                $media->convert_video();
            }

            $item->media_id = $media->id;
        }

        $item->save();
        $item->portal->update([
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        if($item->strain) {
            $item->strain->update(['updated_at' => date('Y-m-d H:i:s')]);
        }

        $data = [
            'status' => '200',
            'data' => $item,
        ];

        return response()->json($data);
    }

    public function update_menu(Request $request) {
        $request->validate([
            'category' => 'required',
        ]);        
        $item = Menu::find($request->get('menu_id'));
        $item->portal_id = $request->get('portal_id');
        $item->category_id = $request->get('category');
        $item->strain_id = $request->get('strain');
        $item->item_name = $request->get('item_name');
        $item->price_gram = $request->get('price_gram');
        $item->price_half_gram = $request->get('price_half_gram');
        $item->price_eighth = $request->get('price_eighth');
        $item->price_quarter = $request->get('price_quarter');
        $item->price_half_oz = $request->get('price_half_oz');
        $item->price_oz = $request->get('price_oz');
        $item->price_each = $request->get('price_each');
        $media = Media::find($item->media_id);
        if($media) {
            $media->update(['created_at' => date('Y-m-d H:i:s')]);
            if($item->description != $request->get('description')){
                $media->update(['description' => $request->get('description')]);
            }
        }
        $item->description = $request->get('description');

        if($request->get('strain')) {
            $strain_id = $request->get('strain');
            $strain_name = Strain::find($strain_id)->name;
        } else {
            $strain_name = $request->get('item_name');
        }
        $file_name = get_filename($strain_name)."_marijuana_".time();

        if($request->hasFile("file") && $request->file('file') != null){
            $picture = request()->file('file');
            $imageName = $file_name.'.'.$picture->getClientOriginalExtension();
            $mime = $picture->getMimeType();
            if(strstr($mime, 'video/')) {
                $file_type = 'video';
                $picture->move(public_path('uploaded/video/'), $imageName);
                $image_url = 'uploaded/video/'.$imageName;
            } elseif (strstr($mime, 'image/')) {
                $file_type = 'image';
                $picture->move(public_path('uploaded/image/'), $imageName);
                $image_url = 'uploaded/image/'.$imageName;
            }

            $media = Media::find($item->media_id);

            if($media) {
                $media->update([
                    'url' => $image_url,
                    'type' => $file_type,                    
                    'description' => $request->get('description'),
                ]);
            } else {
                $media = Media::create([
                    'url' => $image_url,
                    'model' => 'menu',
                    'type' => $file_type,
                    'user_id' => $request->get('portal_id'),
                    'description' => $request->get('description'),
                ]);
                $item->media_id = $media->id;
            }

            if($media->type == 'video') {
                $media->convert_video();
            }
        }

        if($request->get('remove_media') != '') {
            $item->media->delete();
        }

        $item->save();
        $item->portal->update([
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if($item->strain) {
            $item->strain->update(['updated_at' => date('Y-m-d H:i:s')]);
        }

        $data = [
            'status' => '200',
            'data' => $item,
        ];

        return response()->json($data);
    }

    public function delete_menu($id) {
        $item = Menu::find($id);
        if($item->media) {
            $item->media->delete();
        }
        Menu::destroy($id);
        $data = [
            'status' => '200',
            'data' => 'success',
        ];
        return response()->json($data);
    }

    public function deactive_menu($id) {
        $item = Menu::find($id);
        $new_status = !$item->is_active;
        $item->is_active = $new_status;
        if($item->media) {
            $item->media->update(['is_active' => $new_status]);
        }
        $item->save();
        $data = [
            'status' => '200',
            'data' => 'success',
        ];

        return response()->json($data);
    }

    // Customized

    public function getPortal(Request $request) {
        $portal = User::find($request->get('id'));
        return response()->json($portal);
    }

    public function getDistance(Request $request) {
        $portal = User::find($request->get('id'));
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $distance = $portal->get_distance($latitude, $longitude);
        return response()->json(['status' => 200, 'distance' => $distance]);
    }

    public function export_cities(Request $request) {
        $cities = City::all();
        $cities = $cities->makeHidden(['id', 'state_id', 'created_at', 'updated_at']);
        return response()->json($cities);
    }
}
