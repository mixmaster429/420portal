<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $with = ['parent', 'user'];
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    public function parent() {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function children() {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function likes() {
        return $this->hasMany(Like::class, 'target_id')->where('model', 'comment');
    }

    public function target() {
        switch ($this->target_model) {
            case 'media':
                return $this->belongsTo(Media::class, 'target_id');
                break;

            case 'portal':
                return $this->belongsTo(Portal::class, 'target_id');
                break;

            case 'strain':
                return $this->belongsTo(Strain::class, 'target_id');
                break;

            case 'news':
                return $this->belongsTo(Post::class, 'target_id');
                break;
            
            default:
                return $this->belongsTo(Media::class, 'target_id');
                break;
        }
    }

}
