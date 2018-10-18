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
 * @property bool $is_gif
 * @property bool $is_video
 * @property bool $video_group_id
 * @property bool $video_id
 * @property bool $shared_group_id
 * @property bool $shared_post_id
 * @property string $export_hash
 *
 * @property Group $group
 */
class Post extends Model
{
    protected $fillable = [
        'group_id', 'post_id', 'date', 'likes', 'shares', 'views', 'comments', 'links', 'is_pinned', 'is_ad', 'is_gif',
        'is_video', 'video_group_id', 'video_id', 'shared_group_id', 'shared_post_id', 'export_hash',
    ];

    protected $dates = [
        'date',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Group
     */
    public function group(): Group
    {
        return $this->belongsTo(Group::class);
    }

    public function getUrlAttribute(): string
    {
        return 'https://vk.com/wall-' . $this->group->source_id . '_' . $this->post_id;
    }
}
