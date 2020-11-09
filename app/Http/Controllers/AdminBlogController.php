<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePost;
use App\Models\NewsCategory;
use App\Models\NewsCategoryPost;
use App\Models\Post;
use Illuminate\Http\Request;

class AdminBlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $mod = new Post();
        $keyword = '';
        if($request->get('keyword') != '') {
            $keyword = $request->get('keyword');
            $mod = $mod->where('title', 'like', "%$keyword%");
        }

        $posts = $mod->orderBy('created_at', 'desc')->paginate(4);
        return view('admin.blog.index', compact('posts', 'keyword'));
    }
    
    public function create()
    {
        $categories = NewsCategory::orderBy('order', 'asc')->get();
        return view('admin.blog.add', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $post = new Post();
        $post->title = $request->title;
        $post->slug = convertNameToSlug($request->title);
        $post->description = $request->description;
        $post->category = implode(',', $request->categories);
        $post->meta_title = $request->meta_title;
        $post->meta_description = $request->meta_description;
        $post->meta_keywords = $request->meta_keywords;
        // if($request->hasFile('image'))
        //     $post->image = upload_file($request->file('image'), 'news');
        $post->image = $request->get('image');
        $post->save();
        $post->categories()->attach($request->categories);        
        return response()->json(['status' => 200, 'post' => $post]);
    }

    public function edit($id)
    {
        $categories = NewsCategory::orderBy('order', 'asc')->get();
        $post = Post::find($id);
        return view('admin.blog.add', compact('post', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        $post->title = $request->title;
        $post->slug = convertNameToSlug($request->title);
        $post->description = $request->description;
        $post->category = implode(',', $request->categories);
        $post->meta_title = $request->meta_title;
        $post->meta_description = $request->meta_description;
        $post->meta_keywords = $request->meta_keywords;
        // if($request->hasFile('image'))
        //     $post->image = upload_file($request->file('image'), 'news');
        $post->image = $request->get('image');
        $post->save();
        $post->categories()->sync($request->categories);
        return response()->json(['status' => 200, 'post' => $post]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        remove_file($post->image);
        $post->delete();
        return response()->json(['status' => 200]);
    }
}
