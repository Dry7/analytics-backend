<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Post
 * @package App\Models
 *
 * @property int $id
 * @property int $group_id
 * @property int $post_id
 * @property Carbon $date
 * @property int $likes
 * @property int $shares
 * @property int $views
 * @property int $comments
 * @property int $links
 * @property bool $is_pinned
 * @property bool $is_ad
 * @property string $export_hash
 *
 * @property Group $group
 */
class Post extends Model
{
    protected $fillable = [
        'group_id', 'post_id', 'date', 'likes', 'shares', 'views', 'comments', 'links', 'is_pinned', 'is_ad', 'export_hash',
    ];

    protected $dates = [
        'date',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function getUrlAttribute()
    {
        return 'https://vk.com/wall-' . $this->group->source_id . '_' . $this->post_id;
    }
}
