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
        $total_count = Post::count();
        $mod = new Post();
        $mod = $mod->withCount('comments');
        if($request->get('category_id') != ''){
            $category_id = $request->get('category_id');
            $category = NewsCategory::find($category_id);
            $mod = $category->posts();
        }

        $posts = $mod->orderBy('created_at', 'desc')->get();
        return response()->json(['categories' => $categories, 'posts' => $posts, 'total_count' => $total_count]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug) {
        $post = Post::where('slug', $slug)->first();
        $post->load('comments');
        $categories = NewsCategory::withCount('posts')->orderBy('order', 'asc')->get();
        return response()->json(['post' => $post, 'categories' => $categories]);
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
