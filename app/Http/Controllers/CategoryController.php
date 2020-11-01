<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Strain;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('category.show');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->middleware('auth');

        $validateRequest = $this->validate($request, [
            'description' => 'required',
        ]);

        $category = Category::findOrFail($id);

        $category->description = $request->description;
        $category->save();

        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

    public function get_all_categories(Request $request) {
        return response()->json(Category::orderBy('order')->get());
    }

    public function get_strains(Request $request) {
        // $item = Category::find($request->get('id'));
        $data = Strain::all();
        return response()->json($data);
    }

    public function getCategoryStrains(Request $request) {
        if($request->has('target_id')){          
            $category =  Category::find($request->get('target_id'));  
        }
        if($request->has('slug')){
            $category = Category::whereSlug($request->get('slug'))->first();
        }
        $per_page = $request->get('per_page');
        $strains = $category->strains()->paginate($per_page);
        foreach ($strains as $item) {
            $item->main_image = $item->get_main_media() ? $item->get_main_media()->url : '/imgs/strains/blue.jpg';
        }
        $data = [
            'category' => $category,
            'strains' => $strains,
        ];
        return response()->json($data);
    }
}
