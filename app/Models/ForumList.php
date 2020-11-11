<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumList extends Model
{
    protected $guarded = [];

    protected $with = ['likes', 'getMParent'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function children()
    {
        return $this->hasMany('App\Models\ForumList','mparent');
    }
    public function getSParent()
    {
        return $this->belongsTo('App\Models\ForumList','sparent');
    }
    public function getMParent()
    {
        return $this->belongsTo('App\Models\ForumList','mparent');
    }
    public function schildren()
    {
        return $this->hasMany('App\Models\ForumList','sparent');
    }

    public function likes() {
        return $this->hasMany(ForumLike::class, 'topic_id');
    }

    // ---- xian relationships ----
    public function s_parent() {
        return $this->belongsTo('App\Models\ForumList','sparent');
    }
    public function m_parent() {
        return $this->belongsTo('App\Models\ForumList','mparent');
    }

    public function m_children() {
        return $this->hasMany('App\Models\ForumList','mparent');
    }

    public function s_children() {
        return $this->hasMany('App\Models\ForumList','sparent');
    }
}
