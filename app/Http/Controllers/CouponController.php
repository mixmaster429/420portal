<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\User;
use App\Models\Category;
use App\Models\Strain;
use App\Models\Media;
use App\Models\Coupon;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        return view('coupon.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Coupon $coupon
     * @return Response
     */
    public function show(Request $request)
    {
        $userLatitude = $request->lat;
        $userLongitude = $request->lng;
        $inactive_portals = User::whereType('company')->where('is_active', 0)->pluck('id')->toArray();
        $coupons = Coupon::with('portal')->whereNotIn('portal_id', $inactive_portals)->get();
        foreach ($coupons as $key => $item) {
            $item->portal->profilePic;
            $item->portal->shop_status = $item->portal->get_shop_status();
            $item->distance = $item->portal->get_distance($userLatitude, $userLongitude);
        }

        $sorted = $coupons->sortBy('distance')->values()->all();
        return response()->json($sorted);
    }
    
    public function store(Request $request) {
        $coupon = Coupon::create([
            'portal_id' => $request->get('portal_id'),
            'category_id' => $request->get('category_id'),
            'strain_id' => $request->get('strain_id'),
            'brand_name' => $request->get('brand_name'),
            'description' => $request->get('description'),
        ]);
        $portal = User::find($coupon->portal_id);
        if($request->get('media_url') != '') {
            $media = Media::create([
                'user_id' => $portal->id,
                'type' => 'image',
                'model' => 'coupon',
                'tagged_strain' => $request->get('strain_id'),
                'url' => $request->get('media_url'),
                'description' => $request->get('description'),
            ]);
            $coupon->media_id = $media->id;
            $coupon->save();
        }
        return response()->json($coupon->load('category', 'strain', 'media'));
    }

    public function update(Request $request, $id) {
        $this->destroy($id);
        $coupon = Coupon::create([
            'portal_id' => $request->get('portal_id'),
            'category_id' => $request->get('category_id'),
            'strain_id' => $request->get('strain_id'),
            'brand_name' => $request->get('brand_name'),
            'description' => $request->get('description'),
        ]);
        $portal = User::find($coupon->portal_id);
        if($request->get('media_url') != '') {
            $media = Media::create([
                'user_id' => $portal->id,
                'type' => 'image',
                'model' => 'coupon',
                'tagged_strain' => $request->get('strain_id'),
                'url' => $request->get('media_url'),
                'description' => $request->get('description'),
            ]);
            $coupon->media_id = $media->id;
            $coupon->save();
        }
        // $coupon->update([
        //     'category_id' => $request->get('category_id'),
        //     'strain_id' => $request->get('strain_id'),
        //     'brand_name' => $request->get('brand_name'),
        //     'description' => $request->get('description'),
        // ]);
        // $portal = $coupon->portal;
        // $media = $coupon->media;
        // if($request->get('media_url') != '') {
        //     if(!$media || $media->url != $request->get('media_url')) {
        //         if($media && $media->url != $request->get('media_url')) {
        //             $media->delete();
        //         }
        //         $coupon_media = Media::create([
        //             'user_id' => $portal->id,
        //             'type' => 'image',
        //             'model' => 'coupon',
        //             'url' => $request->get('media_url'),
        //             'tagged_strain' => $request->get('strain_id'),
        //             'description' => $request->get('description'),
        //         ]);
        //         $coupon->media_id = $coupon_media->id;
        //         $coupon->save();
        //     } else {
        //         $coupon->media->update([
        //             'updated_at' => date('Y-m-d H:i:s'),
        //         ]);
        //     }
        // }
        return response()->json($coupon->load('category', 'strain', 'media'));
    }

    public function destroy($id) {
        $coupon = Coupon::find($id);
        if($coupon->media) {
            $coupon->media->delete();
        }
        $coupon->delete();
        return response()->json('success');
    }
}
