<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Strain;
use App\User;
use App\Models\Portal;
use App\Models\State;
use App\Models\City;
use App\Models\ForumList;
use App\Models\Post;
use App\Models\Media;

class SitemapController extends Controller
{
    public function index()
    {
        return response()->view('sitemap.index')->header('Content-Type','text/xml');
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
