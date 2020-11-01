<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\StoreNewsCategory;
use App\Models\NewsCategory;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = NewsCategory::orderBy('order')->paginate(4);
        $max = $this->getNewOrder()-1;
        return view('admin.category.index', compact('categories', 'max'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNewsCategory $request)
    {
        $request->validated();
        $category = new NewsCategory();
        $category->name = $request->name;
        $category->slug = convertNameToSlug($request->name);
        $category->order = $this->getNewOrder();
        $category->save();

        return back()->with('message', 'Added category');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        $categories = NewsCategory::orderBy('order')->get();
        $category = NewsCategory::where('slug', $slug)->first();
        $max = $this->getNewOrder()-1;
        return view('admin.category.index')
            ->with('category', $category)
            ->with('max', $max)
            ->with('categories', $categories);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreNewsCategory $request, $slug)
    {
        $request->validated();
        $category = NewsCategory::where('slug', $slug)->first();
        $category->name = $request->name;
        $category->slug = convertNameToSlug($request->name);
        $category->save();

        return redirect()->route('admin.category');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $remarkable_order = NewsCategory::find($id)->order;
        NewsCategory::destroy($id);
        $max = $this->getNewOrder();
        // remove the news related to the category
        $posts = Post::all();
        foreach($posts as $post){
            if(in_array($id, explode(',', $post->category)))
                $post->delete();
        }

        if($max-$remarkable_order > 1){
            for($i=$remarkable_order+1; $i<$max;$i++){
                $category = NewsCategory::where('order', $i)->first();
                $category->order = $i-1;
                $category->save();
            }
        }
        return redirect()->back()->with('message', 'Deleted category');
    }

    /**
     * Return new order.
     *
     * @return int $newOrder
     */
    public function getNewOrder()
    {
        $order = NewsCategory::max('order');
        if(!$order)
            return 1;
        return NewsCategory::max('order')+1;
    }

    /**
     * Reordering as up
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function upOrder($id)
    {
        $category = NewsCategory::find($id);
        $remarkable_order = $category->order - 1;
        $changeWillCategory = NewsCategory::where('order', $remarkable_order)->first();
        $category->order = $remarkable_order;
        $category->save();

        $changeWillCategory->order = $remarkable_order + 1;
        $changeWillCategory->save();

        return redirect()->back()->with('message', 'Changed category order');
    }

    /**
     * Reordering as down
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downOrder($id)
    {
        $category = NewsCategory::find($id);
        $remarkable_order = $category->order + 1;
        $changeWillCategory = NewsCategory::where('order', $remarkable_order)->first();
        $category->order = $remarkable_order;
        $category->save();

        $changeWillCategory->order = $remarkable_order - 1;
        $changeWillCategory->save();

        return back()->with('message', 'Changed category order');
    }
}
