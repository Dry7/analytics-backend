<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['network_id', 'type_id', 'avatar', 'title', 'source_id', 'slug', 'members', 'cpp',
        'is_verified', 'is_closed', 'is_adult', 'is_banned', 'in_search', 'posts', 'opened_at', 'last_post_at'
    ];
}
