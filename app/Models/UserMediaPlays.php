<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMediaPlays extends Model
{
    protected $fillable = ['user_id', 'media_id'];
}
