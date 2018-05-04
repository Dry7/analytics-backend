<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Post
 * @package App\Models
 *
 * @property int $id
 * @property int $group_id
 * @property Carbon $date
 * @property int $likes
 * @property int $shares
 * @property int $views
 * @property int $comments
 */
class Post extends Model
{
    protected $fillable = [
        'group_id', 'post_id', 'date', 'likes', 'shares', 'views', 'comments',
    ];

    protected $dates = [
        'date',
    ];
}
