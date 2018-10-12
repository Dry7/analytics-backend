<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Contact
 * @package App\Models
 *
 * @property int $id
 * @property int $group_id
 * @property string|null $avatar
 * @property string $url
 * @property string $name
 * @property boolean $active
 */
class Contact extends Model
{
    protected $fillable = ['group_id', 'avatar', 'url', 'name', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
