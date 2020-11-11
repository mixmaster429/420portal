<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Strain;
use App\Models\Coupon;
use App\User;
use App\Models\Portal;
use App\Models\State;
use App\Models\City;
use App\Models\ForumList;
use App\Models\Post;
use App\Models\Media;

use Carbon\Carbon;

class SitemapController extends Controller
{
    public function index() {
        $routes = [];
        $current_date = Carbon::now()->toAtomString();
        
        $last_created_media = Media::orderBy('created_at', 'desc')->first();
        $lastmod = $last_created_media ? $last_created_media->created_at->toAtomString() : $current_date;
        array_push($routes, ['url' => '', 'lastmod' => $lastmod, 'priority' => 0.9 ]);
        
        $last_created_company = User::where('type', 'company')->orderBy('created_at', 'desc')->first();
        $lastmod = $last_created_company ? $last_created_company->created_at->toAtomString() : $current_date;
        array_push($routes, ['url' => '/medical-recreational-marijuana-dispensary-delivery', 'lastmod' => $last_created_company->created_at->toAtomString(), 'priority' => 0.9 ]);
        
        array_push($routes, ['url' => '/marijuana-strains', 'priority' => 0.9 ]);
        
        $last_updated_coupon = Coupon::orderBy('updated_at', 'desc')->first();
        $lastmod = $last_updated_coupon ? $last_updated_coupon->updated_at->toAtomString() : $current_date;
        array_push($routes, ['url' => '/marijuana-coupons', 'lastmod' => $lastmod, 'priority' => 0.9 ]);

        array_push($routes, ['url' => '/marijuana-strains/indica', 'priority' => 0.9 ]);
        array_push($routes, ['url' => '/marijuana-strains/sativa', 'priority' => 0.9 ]);
        array_push($routes, ['url' => '/marijuana-strains/hybrid', 'priority' => 0.9 ]);

        $last_created_forum = ForumList::orderBy('created_at', 'desc')->first();
        $lastmod = $last_created_forum ? $last_created_forum->updated_at->toAtomString() : $current_date;
        array_push($routes, ['url' => '/marijuana-forums', 'lastmod' => $lastmod, 'priority' => 0.9 ]);

        $last_created_brand = User::where('type', 'brand')->orderBy('created_at', 'desc')->first();
        $lastmod = $last_created_brand ? $last_created_brand->updated_at->toAtomString() : $current_date;
        array_push($routes, ['url' => '/marijuana-brands', 'lastmod' => $lastmod, 'priority' => 0.9 ]);

        return response()->json($routes);
    }

    public function strains()
    {
        $strains = Strain::all();
        return response()->view('sitemap.strains',[
            'strains' => $strains,
        ])->header('Content-Type','text/xml');
    }

    public function users()
    {
        $users = User::whereType('user')->where('is_active','1')->get();
        return response()->view('sitemap.users',[
            'users' => $users,
        ])->header('Content-Type','text/xml');
    }

    public function companies()
    {
        $companies = User::whereType('company')->where('is_active', '1')->get();
        return response()->view('sitemap.companies',[
            'companies' => $companies,
        ])->header('Content-Type', 'text/xml');
    }

    public function states()
    {
        $states = State::all();
        $cities = City::all();
        return response()->view('sitemap.states',[
            'states' => $states,
        ])->header('Content-Type','text/xml');
    }
    public function forums()
    {
        $forums = ForumList::where('mparent', '0')->get();        
        return response()->view('sitemap.forum',[
            'forums' => $forums,
        ])->header('Content-Type','text/xml');
    }
    public function news()
    {
        $data = Post::all();        
        return response()->view('sitemap.news', compact('data'))->header('Content-Type','text/xml');
    }
    public function media()
    {
        $data = Media::all();        
        return response()->view('sitemap.media', compact('data'))->header('Content-Type','text/xml');
    }
}
