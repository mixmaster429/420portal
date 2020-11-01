<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumLike extends Model
{
    protected $guarded = [];

    public function topic() {
        return $this->belongsTo(ForumList::class, 'topic_id');
    }
}
