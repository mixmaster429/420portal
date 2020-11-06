<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockUser extends Model
{
    protected $table = 'blockusers';
    
    protected $fillable = [
        'loggeduser', 'blockeduser'
    ];
}
