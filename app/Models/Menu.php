<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $guarded = [];

    protected $with = ['media', 'strain', 'portal', 'category'];

    protected $appends = ["show_description"];

    public function getShowDescriptionAttribute() {
        return 0;
    }

    public function Portal() {
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
