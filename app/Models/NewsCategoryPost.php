<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsCategoryPost extends Model
{
    protected $guaded = [];
    
    public function category() {
        return $this->belongsTo(NewsCategory::class);
    }

    public function post(){
        return $this->belongsTo(Post::class);
    }
}
