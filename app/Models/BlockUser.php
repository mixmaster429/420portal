<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockUser extends Model
{
    protected $table = 'blockuser';
    
    protected $fillable = [
        'loggeduser', 'blockeduser'
    ];
}
