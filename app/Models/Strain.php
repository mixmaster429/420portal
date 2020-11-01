<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Strain extends Model
{
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function taggedMedia()
    {
        return $this->belongsTo(Media::class, 'tagged_strain');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function menus() {
        return $this->hasMany(Menu::class);
    }

    public function likes() {
        return $this->hasMany(Like::class, 'target_id')->where('model', 'strain');
    }

    public function is_like() {
        return $this->likes()->where('user_id', auth()->id())->count();
    }

    public function comments() {
        return $this->hasMany(Comment::class, 'target_id')->where('target_model', 'strain');
    }

    public function get_main_media() {

        $strain_menus = $this->menus->pluck('media_id')->unique()->toArray();
        $strain_menus = array_filter($strain_menus);
        $id = $this->id;
        $main_media = Media::with('likes')->withCount('likes')
                        ->whereType('image')
                        ->where('is_active', 1)
                        ->where(function($query) use($id, $strain_menus){
                            return $query->where('tagged_strain', $id)->orWhereIn('id', $strain_menus);
                        })
                        ->orderBy('likes_count', 'desc')
                        ->first();
        return $main_media;
    }
}
