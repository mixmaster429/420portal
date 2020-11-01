<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Follow;

class Notification extends Model
{
    protected $guarded = [];

    protected $with = ['notifier', 'notifiable'];

    protected $appends = ['is_follower'];

    public function getIsFollowerAttribute() {
        $user_id = $this->user_id;
        $follower_user_id = $this->notifier_id;
        $count = Follow::where('user_id', $user_id)->where('follower_user_id', $follower_user_id)->count();
        return $count;
    }

    public function notifiable() {
        return $this->morphTo();
    }

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function notifier(){
        return $this->belongsTo('App\User', 'notifier_id');
    }
}
