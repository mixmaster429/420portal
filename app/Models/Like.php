<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $guarded = [];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function target() {
        switch ($this->model) {
            case 'post':
                return $this->belongsTo(Media::class, 'target_id');
                break;

            case 'portal':
                return $this->belongsTo(Portal::class, 'target_id');
                break;

            case 'strain':
                return $this->belongsTo(Strain::class, 'target_id');
                break;

            case 'comment':
                return $this->belongsTo(Comment::class, 'target_id');
                break;
            
            default:
                return $this->belongsTo(Media::class, 'target_id');
                break;
        }
    }
}
