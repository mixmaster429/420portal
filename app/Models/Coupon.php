<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $guarded = [];

    protected $with = ['category', 'strain', 'media'];

    public function portal() {
        return $this->belongsTo('App\User', 'portal_id');
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }
    
    public function strain() {
        return $this->belongsTo(Strain::class);
    }
    
    public function media() {
        return $this->belongsTo(Media::class);
    }
}
