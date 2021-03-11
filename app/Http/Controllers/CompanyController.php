<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Company;
use App\Models\Brand;
use App\Models\Media;
use App\User;

class CompanyController extends Controller
{
    public function importCompany(Request $request) {
        ini_set('max_execution_time', '0');
        $companies = Company::whereNull('imported')->get();
        // dd($companies->count());
        foreach ($companies as $company) {
            $user = User::create([
                'name' => $company->name,
                'username' => $company->username,
                'slug' => $company->slug,
                'email' => $company->email,
                'password' => bcrypt('420portalpassword'),
                'media_id' => 1,
                'role_id' => 2,
                'description' => $company->description,
                'type' => 'company',
                'address' => $company->address,
                'city' => $company->city,
                'state' => $company->state,
                'postal' => $company->postal,
                'timezone' => $company->timezone,
                'phone_number' => $company->phone_number,
                'latitude' => $company->latitude,
                'longitude' => $company->longitude,
                'website_url' => $company->website_url,
                'facebook_url' => $company->facebook_url,
                'instagram_url' => $company->instagram_url,
                'youtube_url' => $company->youtube_url,
                'suite' => $company->suite,
                'store_type' => $company->store_type,
                'recreational' => $company->recreational,
                'medical' => $company->medical,
                'atm' => $company->atm,
                'security' => $company->security,
                'state_license' => $company->state_license,
                'mon_open' => $company->mon_open,
                'mon_close' => $company->mon_close,
                'mon_closed' => $company->mon_closed,
                'tue_open' => $company->tue_open,
                'tue_close' => $company->tue_close,
                'tue_closed' => $company->tue_closed,
                'wed_open' => $company->wed_open,
                'wed_close' => $company->wed_close,
                'wed_closed' => $company->wed_closed,
                'thu_open' => $company->thu_open,
                'thu_close' => $company->thu_close,
                'thu_closed' => $company->thu_closed,
                'fri_open' => $company->fri_open,
                'fri_close' => $company->fri_close,
                'fri_closed' => $company->fri_closed,
                'sat_open' => $company->sat_open,
                'sat_close' => $company->sat_close,
                'sat_closed' => $company->sat_closed,
                'sun_open' => $company->sun_open,
                'sun_close' => $company->sun_close,
                'sun_closed' => $company->sun_closed,
                'from_weedmap' => 1,
                'package_level' => $company->package_level,
            ]);
            $media = Media::create([
                'url' => "/uploaded/image/".$company->image,
                'type' => 'image',
                'description' => $company->description,
                'model' => 'logo',
                'user_id' => $user->id,
            ]);
            $user->update(['media_id' => $media->id]);
            $company->update(['imported' => 1]);
        }
    }

    public function checkOpen() {
        // dd(Media::where('url', '/uploaded/image/')->count());
        // $empty_medias = Media::where('url', '/uploaded/image/')->pluck('user_id');
        // User::whereIn('id', $empty_medias)->update(['media_id' => null]);
        // Media::where('url', '/uploaded/image/')->delete();
        $user = User::find(5373);
        dump($user->get_shop_status());
    }

    public function solveClosed() {
        ini_set('max_execution_time', '0');
        $day_array = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        foreach ($day_array as $day) {
          
            $open_companies = User::where('from_weedmap', 1)->where($day.'_closed', 1)->get();
            $closed_companies = User::where('from_weedmap', 1)->where($day.'_closed', 2)->get();
            // dump($day, $companies->count());
            foreach ($open_companies as $item) {
                $item->update([$day.'_closed' => 2]);
            }
            foreach ($closed_companies as $item) {
                $item->update([$day.'_closed' => 1]);
            }
        }
        dd('ok');
    }
    public function importBrand(Request $request) {
        ini_set('max_execution_time', '0');
        $brands = Brand::whereNull('imported')->get();
        // dd($brands->count());
        foreach ($brands as $brand) {
            $user = User::create([
                'name' => $brand->name,
                'username' => $brand->username,
                'slug' => $brand->slug,
                'password' => bcrypt('420portalpassword'),
                'description' => '',
                'media_id' => 1,
                'role_id' => 2,
                'type' => 'brand',
                'website_url' => $brand->website_url,
                'facebook_url' => $brand->facebook_url,
                'instagram_url' => $brand->instagram_url,
                'youtube_url' => $brand->youtube_url,
                'from_weedmap' => 1,
            ]);
            $media = Media::create([
                'url' => "/uploaded/image/".$brand->image,
                'type' => 'image',
                'model' => 'logo',
                'user_id' => $user->id,
                'description' => '',
            ]);
            $user->update(['media_id' => $media->id]);
            $brand->update(['imported' => 1]);
        }
    }

    public function downloadEmptyImages() {
        ini_set('max_execution_time', '0');
        $companies = Company::where('avatar_image', '!=', '')->whereNull('image')->get();
        foreach ($companies as $item) {
            $url = $item->avatar_image;
            $info = pathinfo($url);
            try {
                $contents = file_get_contents($url);
                $extension = isset($info['extension']) ? $info['extension'] : 'jpg';
                $new_file_name = $item->username."_marijuana_".time();
                $file = public_path('company/'.$new_file_name.".".$extension);
                file_put_contents($file, $contents);  
                $item->update(['image' => $new_file_name.".".$extension]);
                $user = User::where('username', $item->username)->first();
                if($user) {
                    $media = Media::create([
                        'url' => "/uploaded/image/".$item->image,
                        'type' => 'image',
                        'description' => '',
                        'model' => 'logo',
                        'user_id' => $user->id,
                    ]);
                    $user->update(['media_id' => $media->id]);
                }
            } catch (\Throwable $th) {
                throw $th;
                // dump($item->id);
            }
        }
    }
}
