<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Strain;
use App\Models\Coupon;
use App\User;
use App\Models\Portal;
use App\Models\State;
use App\Models\City;
use App\Models\Post;
use App\Models\Media;
use App\Models\ForumList;

use Carbon\Carbon;

class SitemapController extends Controller
{
    public function index() {
        $routes = [];
        $current_date = Carbon::now()->toAtomString();
        
        // $last_created_media = Media::orderBy('created_at', 'desc')->first();
        // $lastmod = $last_created_media ? $last_created_media->created_at->toAtomString() : $current_date;
        array_push($routes, ['url' => '', 'priority' => 0.9 ]);
        
        // $last_created_company = User::where('type', 'company')->orderBy('created_at', 'desc')->first();
        // $lastmod = $last_created_company ? $last_created_company->created_at->toAtomString() : $current_date;
        array_push($routes, ['url' => '/medical-recreational-marijuana-dispensary-delivery', 'priority' => 0.9 ]);
        
        array_push($routes, ['url' => '/marijuana-strains', 'priority' => 0.9 ]);
        
        // $last_updated_coupon = Coupon::orderBy('updated_at', 'desc')->first();
        // $lastmod = $last_updated_coupon ? $last_updated_coupon->updated_at->toAtomString() : $current_date;
        array_push($routes, ['url' => '/marijuana-coupons', 'priority' => 0.9 ]);

        array_push($routes, ['url' => '/marijuana-strains/indica', 'priority' => 0.9 ]);
        array_push($routes, ['url' => '/marijuana-strains/sativa', 'priority' => 0.9 ]);
        array_push($routes, ['url' => '/marijuana-strains/hybrid', 'priority' => 0.9 ]);

        // $last_created_forum = ForumList::orderBy('created_at', 'desc')->first();
        // $lastmod = $last_created_forum ? $last_created_forum->created_at->toAtomString() : $current_date;
        array_push($routes, ['url' => '/marijuana-forums', 'priority' => 0.9 ]);

        // $last_created_brand = User::where('type', 'brand')->orderBy('created_at', 'desc')->first();
        // $lastmod = $last_created_brand ? $last_created_brand->created_at->toAtomString() : $current_date;
        array_push($routes, ['url' => '/marijuana-brands', 'priority' => 0.9 ]);

        return response()->json($routes);
    }

    public function strains() {
        $strains = Strain::all();
        $routes = [];

        foreach ($strains as $item) {
            array_push($routes, ['url' => '/marijuana-strains/'.$item->slug, 'priority' => 0.9 ]);
        }

        return response()->json($routes);
    }

    public function users() {
        $users = User::whereType('user')->where('is_active','1')->get();
        $routes = [];

        foreach ($users as $item) {
            array_push($routes, ['url' => '/'.$item->name, 'priority' => 0.9 ]);
        }

        return response()->json($routes);
        
    }

    public function companies() {
        $companies = User::whereType('company')->where('is_active', '1')->get();
        $routes = [];

        foreach ($companies as $item) {
            array_push($routes, ['url' => '/'.$item->username, 'priority' => 0.9 ]);
        }

        return response()->json($routes);
    }

    public function states() {
        $states = State::all();$routes = [];
        $routes = [];

        foreach ($states as $state) {
            array_push($routes, ['url' => '/medical-recreational-marijuana-dispensary-delivery/'.strtolower(str_replace(' ', '-', $state->name)), 'priority' => 0.9 ]);
            foreach ($state->cities as $city) {
                array_push($routes, ['url' => '/medical-recreational-marijuana-dispensary-delivery/'.strtolower(str_replace(' ', '-', $state->name)).'/'.strtolower(str_replace(' ', '-', $city->name)), 'priority' => 0.9 ]);
            }
        }

        return response()->json($routes);        
    }
    public function forums() {
        $forums = ForumList::where('mparent', '0')->get();
        $routes = [];

        array_push($routes, ['url' => '/marijuana-forums', 'priority' => 0.9 ]);
        foreach ($forums as $item) {
            array_push($routes, ['url' => '/marijuana-forums/'.convertNameToSlug($item->title).'/'.$item->id, 'priority' => 0.9 ]);
        }

        return response()->json($routes);
    }
    public function news() {
        $posts = Post::all();
        $routes = [];
        // $last_updated_news = Post::orderBy('updated_at', 'desc')->first();
        // $lastmod = $last_updated_news ? $last_updated_news->created_at->toAtomString() : Carbon::now()->toAtomString();
        array_push($routes, ['url' => '/marijuana-news', 'priority' => 0.9 ]);
        foreach ($posts as $item) {
            array_push($routes, ['url' => '/marijuana-news/'.$item->slug, 'priority' => 0.9 ]);
        }

        return response()->json($routes);
    }
}
