<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsCategory extends Model
{
    protected $guaded = [];

    public function posts(){
        return $this->belongsToMany(Post::class, 'news_category_posts', 'news_category_id', 'post_id');
    }
}
