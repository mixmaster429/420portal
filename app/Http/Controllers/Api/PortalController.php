<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Media;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Menu;
use App\Models\Strain;
use App\Models\Category;
use App\User;

use Auth;
use Mail;
use App\Mail\PortalRegisterMail;
use App\Mail\StrainRequestMail;

use Carbon\Carbon;

class PortalController extends Controller
{
    public function getPortal(Request $request) {
        $id = $request->get('id');
        $user_id = auth()->user() ? auth()->id() : null;
        $portal = User::with('medias', 'taggedPortalMedia', 'coupon', 'menus')->find($id);
        if($portal->is_active != 1 && $user_id != $portal->id && $user_id != 1) {
            return response()->json(['success' => false, 'message' => 'Inactive Portal']);
        }

        $portal->posts_count = Media::where('is_active', 1)
            ->where(function($query) use($portal) {
                return $query->where('user_id', $portal->id)
                        ->orWhere('tagged_portal', $portal->id);
            })->count();

        $portal->totalComments = Comment::where('target_id', $portal->id)->where('target_model', 'portal')->count();
        if($portal->type == 'company') {
            $portal->shop_status = $portal->get_shop_status();
        }
        return response()->json(['success' => true, 'data' => $portal]);
    }

    public function store(Request $request) {
        $portal = new User();
        $portal->type = 'company';
        $portal->store_type = $request->get('store_type');
        $portal->name = $request->get('name');
        $portal->username = $request->get('username');
        $portal->password = bcrypt($request->get('password'));
        $portal->slug = str_slug($request->get('name'));
        $portal->address = $request->get('address');
        $portal->city = $request->get('city');
        $portal->state = $request->get('state');
        $portal->postal = $request->get('postal');
        $portal->timezone = $request->get('timezone');
        $portal->phone_number = $request->get('phone_number');
        $portal->latitude = $request->get('latitude');
        $portal->longitude = $request->get('longitude');
        $portal->website_url = $request->get('website_url');
        $portal->facebook_url = $request->get('facebook_url');
        $portal->twitter_url = $request->get('twitter_url');
        $portal->instagram_url = $request->get('instagram_url');
        $portal->youtube_url = $request->get('youtube_url');
        $portal->email = $request->get('email');
        $portal->suite = $request->get('suite');
        $portal->state_license = $request->get('state_license');
        $portal->recreational = $request->get('recreational');
        $portal->medical = $request->get('medical');
        $portal->atm = $request->get('atm');
        $portal->security = $request->get('security');
        $portal->description = $request->get('description');
        $portal->email = $request->get('email');

        $mon_closed = 0;
        if($request->get('monday_opened') == true) $mon_closed = 2;
        if($request->get('monday_closed') == true) $mon_closed = 1;
        $portal->mon_closed = $mon_closed;
        if($request->get('monday_opened') == false && $request->get('monday_closed') == false) {
            $portal->mon_open = $request->get('monday_open_time');
            $portal->mon_close = $request->get('monday_close_time');
        }

        $tue_closed = 0;
        if($request->get('tuesday_opened') == true) $tue_closed = 2;
        if($request->get('tuesday_closed') == true) $tue_closed = 1;
        $portal->tue_closed = $tue_closed;
        if($request->get('tuesday_opened') == false && $request->get('tuesday_closed') == false) {
            $portal->tue_open = $request->get('tuesday_open_time');
            $portal->tue_close = $request->get('tuesday_close_time');
        }

        $wed_closed = 0;
        if($request->get('wednesday_opened') == true) $wed_closed = 2;
        if($request->get('wednesday_closed') == true) $wed_closed = 1;
        $portal->wed_closed = $wed_closed;
        if($request->get('wednesday_opened') == false && $request->get('wednesday_closed') == false) {
            $portal->wed_open = $request->get('wednesday_open_time');
            $portal->wed_close = $request->get('wednesday_close_time');
        }

        $thu_closed = 0;
        if($request->get('thursday_opened') == true) $thu_closed = 2;
        if($request->get('thursday_closed') == true) $thu_closed = 1;
        $portal->thu_closed = $thu_closed;
        if($request->get('thursday_opened') == false && $request->get('thursday_closed') == false) {
            $portal->thu_open = $request->get('thursday_open_time');
            $portal->thu_close = $request->get('thursday_close_time');
        }

        $fri_closed = 0;
        if($request->get('friday_opened') == true) $fri_closed = 2;
        if($request->get('friday_closed') == true) $fri_closed = 1;
        $portal->fri_closed = $fri_closed;
        if($request->get('friday_opened') == false && $request->get('friday_closed') == false) {
            $portal->fri_open = $request->get('friday_open_time');
            $portal->fri_close = $request->get('friday_close_time');
        }

        $sat_closed = 0;
        if($request->get('saturday_opened') == true) $sat_closed = 2;
        if($request->get('saturday_closed') == true) $sat_closed = 1;
        $portal->sat_closed = $sat_closed;
        if($request->get('saturday_opened') == false && $request->get('saturday_closed') == false) {
            $portal->sat_open = $request->get('saturday_open_time');
            $portal->sat_close = $request->get('saturday_close_time');
        }

        $sun_closed = 0;
        if($request->get('sunday_opened') == true) $sun_closed = 2;
        if($request->get('sunday_closed') == true) $sun_closed = 1;
        $portal->sun_closed = $sun_closed;
        if($request->get('sunday_opened') == false && $request->get('sunday_closed') == false) {
            $portal->sun_open = $request->get('sunday_open_time');
            $portal->sun_close = $request->get('sunday_close_time');
        }
        $portal->is_active = 1;
        $portal->save();
        if($request->get('image_url')) {
            $media = new Media();
            $media->user_id = $portal->id;
            $media->type = 'image';
            $media->model = 'logo';
            $media->url = $request->get('image_url');
            $media->description = $request->get('description');
            $media->save();

            $portal->media_id = $media->id;
            $portal->save();
        }

        if($portal->email) {            
            try {
                Mail::to($portal->email)->send(new PortalRegisterMail($portal));
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        return response()->json(['status' => 200, 'portal' => $portal]);
    }

    public function update(Request $request) {
        $portal = User::find($request->get('id'));
        $portal->store_type = $request->get('store_type');
        $portal->name = $request->get('name');
        $portal->username = str_replace(' ', '', $request->get('username'));
        $portal->slug = str_slug($request->get('name'));
        $portal->address = $request->get('address');
        $portal->city = $request->get('city');
        $portal->state = $request->get('state');
        $portal->postal = $request->get('postal');
        $portal->timezone = $request->get('timezone');
        $portal->phone_number = $request->get('phone_number');
        $portal->latitude = $request->get('latitude');
        $portal->longitude = $request->get('longitude');
        $portal->website_url = $request->get('website_url');
        $portal->facebook_url = $request->get('facebook_url');
        $portal->twitter_url = $request->get('twitter_url');
        $portal->instagram_url = $request->get('instagram_url');
        $portal->youtube_url = $request->get('youtube_url');
        $portal->email = $request->get('email');
        $portal->suite = $request->get('suite');
        $portal->state_license = $request->get('state_license');
        $portal->recreational = $request->get('recreational');
        $portal->medical = $request->get('medical');
        $portal->atm = $request->get('atm');
        $portal->security = $request->get('security');
        $portal->description = $request->get('description');
        $portal->email = $request->get('email');

        $mon_closed = 0;
        if($request->get('monday_opened') == true) $mon_closed = 2;
        if($request->get('monday_closed') == true) $mon_closed = 1;
        $portal->mon_closed = $mon_closed;
        if($request->get('monday_opened') == false && $request->get('monday_closed') == false) {
            $portal->mon_open = $request->get('monday_open_time');
            $portal->mon_close = $request->get('monday_close_time');
        }

        $tue_closed = 0;
        if($request->get('tuesday_opened') == true) $tue_closed = 2;
        if($request->get('tuesday_closed') == true) $tue_closed = 1;
        $portal->tue_closed = $tue_closed;
        if($request->get('tuesday_opened') == false && $request->get('tuesday_closed') == false) {
            $portal->tue_open = $request->get('tuesday_open_time');
            $portal->tue_close = $request->get('tuesday_close_time');
        }

        $wed_closed = 0;
        if($request->get('wednesday_opened') == true) $wed_closed = 2;
        if($request->get('wednesday_closed') == true) $wed_closed = 1;
        $portal->wed_closed = $wed_closed;
        if($request->get('wednesday_opened') == false && $request->get('wednesday_closed') == false) {
            $portal->wed_open = $request->get('wednesday_open_time');
            $portal->wed_close = $request->get('wednesday_close_time');
        }

        $thu_closed = 0;
        if($request->get('thursday_opened') == true) $thu_closed = 2;
        if($request->get('thursday_closed') == true) $thu_closed = 1;
        $portal->thu_closed = $thu_closed;
        if($request->get('thursday_opened') == false && $request->get('thursday_closed') == false) {
            $portal->thu_open = $request->get('thursday_open_time');
            $portal->thu_close = $request->get('thursday_close_time');
        }

        $fri_closed = 0;
        if($request->get('friday_opened') == true) $fri_closed = 2;
        if($request->get('friday_closed') == true) $fri_closed = 1;
        $portal->fri_closed = $fri_closed;
        if($request->get('friday_opened') == false && $request->get('friday_closed') == false) {
            $portal->fri_open = $request->get('friday_open_time');
            $portal->fri_close = $request->get('friday_close_time');
        }

        $sat_closed = 0;
        if($request->get('saturday_opened') == true) $sat_closed = 2;
        if($request->get('saturday_closed') == true) $sat_closed = 1;
        $portal->sat_closed = $sat_closed;
        if($request->get('saturday_opened') == false && $request->get('saturday_closed') == false) {
            $portal->sat_open = $request->get('saturday_open_time');
            $portal->sat_close = $request->get('saturday_close_time');
        }

        $sun_closed = 0;
        if($request->get('sunday_opened') == true) $sun_closed = 2;
        if($request->get('sunday_closed') == true) $sun_closed = 1;
        $portal->sun_closed = $sun_closed;
        if($request->get('sunday_opened') == false && $request->get('sunday_closed') == false) {
            $portal->sun_open = $request->get('sunday_open_time');
            $portal->sun_close = $request->get('sunday_close_time');
        }
        $portal->save();
        $media_url = $request->get('image_url');
        if($media_url != '' && strpos($media_url, 'http') == false ) {
            $media = new Media();
            $media->user_id = $portal->id;
            $media->type = 'image';
            $media->model = 'logo';
            $media->url = $media_url;
            $media->description = $request->get('description');
            $media->save();
            $portal->media_id = $media->id;
            $portal->save();
        }
        $data = [
            'status' => 200,
            'data' => $portal,
        ];
        return response()->json($data);
    }

    public function get_all_menus(Request $request) {
        $id = $request->get('id');
        $category_id = $request->get('category_id');
        $price_type = $request->get('price_type');
        $price_max = $request->get('price_max');
        $portal = User::find($id);
        $mod = new Menu();
        if(auth()->id() == $portal->id || auth()->id() == 1) {
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
        $item->category_id = $request->get('category_id');
        $item->strain_id = $request->get('strain_id');
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

        if($request->get("media_url") != ''){

            $media = Media::create([
                'url' => $request->get("media_url"),
                'model' => 'menu',
                'type' => $request->get("media_type"),
                'user_id' => $request->get('portal_id'),
                'description' => $request->get('description'),
            ]);

            // if($media->type == 'video') {
            //     $media->convert_video();
            // }

            $item->media_id = $media->id;
        }

        $item->save();

        $item->portal->update([
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        if($item->strain) {
            $item->strain->update(['updated_at' => date('Y-m-d H:i:s')]);
        }

        // if($request->get('new_strain_request') && $request->get('new_strain')) {
        //     Mail::to('420portal@gmail.com')->send(new StrainRequestMail(Auth::user(), $item));
        // }

        $data = [
            'status' => '200',
            'data' => $item,
        ];

        return response()->json($data);
    }

    public function update_menu(Request $request) {  
        $item = Menu::find($request->get('id'));
        $item->category_id = $request->get('category_id');
        $item->strain_id = $request->get('strain_id');
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
            $media->update([
                'created_at' => date('Y-m-d H:i:s'),
                'description' => $request->get('description'),
            ]);
        }
        $item->description = $request->get('description');

        if($request->get("media_url") != ''){
            
            $media = Media::find($item->media_id);
            if($media) {
                if($request->get('media_url') != $media->url || $request->get('description') != $item->description) {                 
                    $media->update([
                        'url' => $request->get('media_url'),
                        'type' => $request->get('media_type'),                    
                        'description' => $request->get('description'),
                    ]);   
                }
            } else {
                $media = Media::create([
                    'url' => $request->get('media_url'),
                    'model' => 'menu',
                    'type' => $request->get('media_type'),
                    'user_id' => $item->user_id,
                    'description' => $request->get('description'),
                ]);
                $item->media_id = $media->id;
            }

            // if($media->type == 'video') {
            //     $media->convert_video();
            // }
        } else {
            if($item->media) {
                $item->media->delete();
            }
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
}
