<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['network_id', 'type_id', 'avatar', 'title', 'source_id', 'slug', 'members', 'members_possible',
        'is_verified', 'is_closed', 'is_adult', 'is_banned', 'in_search', 'posts', 'country_code', 'state_code', 'city_code',
        'opened_at', 'last_post_at', 'event_start', 'event_end', 'cpp',
    ];

    protected $dates = [
        'opened_at', 'last_post_at', 'event_start', 'event_end',
    ];
}
