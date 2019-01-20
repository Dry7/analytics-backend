<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GroupService
{
    /**
     * @param string|null $title
     * @param int $offset
     * @param int $limit
     *
     * @return Collection
     */
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

    /**
     * @param Group $group
     *
     * @return Collection
     */
    public function links(Group $group): Collection
    {
        return $group->links()->with('post')->get()->sortByDesc('post.date');
    }
}