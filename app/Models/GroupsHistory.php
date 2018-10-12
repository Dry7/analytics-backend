<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupsHistory extends Model
{
    protected $fillable = [
        'group_id', 'date', 'members', 'members_possible', 'posts', 'likes', 'avg_posts', 'avg_comments_per_post',
        'avg_likes_per_post', 'avg_shares_per_post', 'avg_views_per_post'
    ];
}
