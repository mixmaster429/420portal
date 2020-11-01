<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];
    protected $table = 'categories';

    protected $fillable = ['description'];

    public function strains()
    {
        return $this->hasMany('App\Strain')->orderBy('name');
    }
}
