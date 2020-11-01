<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $guarded = [];

    protected $with = ['media'];

    public function menus() {
        return $this->hasMany(Menu::class);
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function media() {
        return $this->belongsTo(Media::class);
    }
}
