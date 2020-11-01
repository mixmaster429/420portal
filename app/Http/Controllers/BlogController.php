<?php

namespace App\Http\Controllers;

use App\Models\NewsCategory;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $request) {
        $categories = NewsCategory::withCount('posts')->orderBy('order', 'asc')->get();

        $mod = new Post();
        $mod = $mod->withCount('comments');
        if($request->get('category_id') != ''){
            $category_id = $request->get('category_id');
            $category = NewsCategory::find($category_id);
            $mod = $category->posts();
        }

        $posts = $mod->orderBy('created_at', 'desc')->get();

        if($request->get('from') == 'app'){
            return response()->json(['categories' => $categories, 'posts' => $posts]);
        } else { 
            return view('blog.index', compact('categories', 'posts'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $post = Post::where('slug', $slug)->first();
        $categories = NewsCategory::withCount('posts')->orderBy('order', 'asc')->get();
        return view('blog.show', compact('categories', 'post'));
    }

    public function getNewsByCategory(Request $request)
    {
        $cagegory = NewsCategory::find($request->get('category_id'));
        $posts = $cagegory->posts()->orderBy('created_at', 'desc')->get();
        $content = '';
        foreach($posts as $post){
            $content .= '<a href="'.route('news.show', $post->slug).'" class="linkDivAnchor">';
            $content .= '<div class="imgsec">';
            $content .= '<img src="'.asset($post->image).'">';
            $content .= '<div class="titlesec">';
            $content .= '<h3 style="color: #efa720;">'.$post->title.'</h3>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</a>';
        }
        return $content;
    }
}
