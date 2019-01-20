<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GroupService
{
    public function getShortList(string $title = null, int $offset = 0, int $limit = 100): Collection
    {
        return Group::query()
            ->select(['id', 'title'])
            ->when(!empty($title), function (Builder $query) use ($title) {
                return $query->where('title', 'ilike', '%' . $title . '%');
            })
            ->offset($offset)
            ->limit($limit)
            ->orderBy('title', 'asc')
            ->get();
    }
}