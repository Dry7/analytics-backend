<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Link
 * @package App\Models
 *
 * @property int $id
 * @property int $group_id
 * @property int $post_id
 * @property string $url
 * @property boolean $is_ad
 */
class Link extends Model
{
    protected $fillable = ['group_id', 'post_id', 'url', 'is_ad'];

    protected $casts = [
        'is_ad' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'post_id');
    }

    public function getPostUrlAttribute()
    {
        return 'https://vk.com/wall-' . $this->group->source_id . '_' . $this->post_id;
    }
}
