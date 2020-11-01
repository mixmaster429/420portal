<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];

    public function categories(){
        return $this->belongsToMany(NewsCategory::class, 'news_category_posts', 'post_id', 'news_category_id');
    }

    public function comments() {
        return $this->hasMany(Comment::class, 'target_id')->where('target_model', 'news');
    }
}
