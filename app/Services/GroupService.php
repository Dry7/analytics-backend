<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Requests\GroupRequest;
use App\Models\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GroupService
{
    /**
     * @param GroupRequest $request
     *
     * @return Collection
     */
    public function groups(GroupRequest $request): Collection
    {
        return Group::query()

        ->when(!empty($request->getTitle()), function (Builder $query) use ($request) {
            return $query->where('title', 'like', '%' . $request->getTitle() . '%');
        })
        ->when($request->getMembersFrom() !== null, function (Builder $query) use ($request) {
            return $query->where('members', '>=', $request->getMembersFrom());
        })
        ->when($request->getMembersTo() !== null, function (Builder $query) use ($request) {
            return $query->where('members', '<=', $request->getMembersTo());
        })
        ->when(!empty($request->getTypeId()), function (Builder $query) use ($request) {
            return $query->whereIn('type_id', $request->getTypeId());
        })
        ->when(!empty($request->getCountry()), function (Builder $query) use ($request) {
            return $query->where('country_code', $request->getCountry());
        })
        ->when(!empty($request->getState()), function (Builder $query) use ($request) {
            return $query->where('state_code', $request->getState());
        })
        ->when(!empty($request->getCity()), function (Builder $query) use ($request) {
            return $query->where('city_code', $request->getCity());
        })
        ->when($request->getIsVerified() !== null, function (Builder $query) use ($request) {
            return $query->where('is_verified', $request->getIsVerified());
        })
        ->when($request->getIsClosed() !== null, function (Builder $query) use ($request) {
            return $query->where('is_closed', $request->getIsClosed());
        })
        ->when($request->getIsAdult() !== null, function (Builder $query) use ($request) {
            return $query->where('is_adult', $request->getIsAdult());
        })
        ->when($request->getPostsFrom(), function (Builder $query) use ($request) {
            return $query->where('posts', '>=', $request->getPostsTo());
        })
        ->when($request->getPostsTo(), function (Builder $query) use ($request) {
            return $query->where('posts_to', '<=', $request->getPostsTo());
        })
        ->when($request->getLikesFrom(), function (Builder $query) use ($request) {
            return $query->where('likes', '>=', $request->getLikesFrom());
        })
        ->when($request->getLikesTo(), function (Builder $query) use ($request) {
            return $query->where('likes', '<=', $request->getLikesTo());
        })
        ->when($request->getAvgPostsFrom(), function (Builder $query) use ($request) {
            return $query->where('avg_posts', '>=', $request->getAvgPostsFrom());
        })
        ->when($request->getAvgPostsTo(), function (Builder $query) use ($request) {
            return $query->where('avg_posts', '<=', $request->getAvgPostsTo());
        })
        ->when($request->getAvgCommentsPerPostFrom(), function (Builder $query) use ($request) {
            return $query->where('avg_comments_per_post', '>=', $request->getAvgCommentsPerPostFrom());
        })
        ->when($request->getAvgCommentsPerPostTo(), function (Builder $query) use ($request) {
            return $query->where('avg_comments_per_post', '<=', $request->getAvgCommentsPerPostTo());
        })
        ->when($request->getAvgLikesPerPostFrom(), function (Builder $query) use ($request) {
            return $query->where('avg_likes_per_post', '>=', $request->getAvgLikesPerPostFrom());
        })
        ->when($request->getAvgLikesPerPostTo(), function (Builder $query) use ($request) {
            return $query->where('avg_likes_per_post', '<=', $request->getAvgLikesPerPostTo());
        })
        ->when($request->getAvgSharesPerPostFrom(), function (Builder $query) use ($request) {
            return $query->where('avg_shares_per_post', '>=', $request->getAvgSharesPerPostFrom());
        })
        ->when($request->getAvgSharesPerPostTo(), function (Builder $query) use ($request) {
            return $query->where('avg_shares_per_post', '<=', $request->getAvgSharesPerPostTo());
        })
        ->when($request->getAvgViewsPerPostFrom(), function (Builder $query) use ($request) {
            return $query->where('avg_views_per_post', '>=', $request->getAvgViewsPerPostFrom());
        })
        ->when($request->getAvgViewsPerPostTo(), function (Builder $query) use ($request) {
            return $query->where('avg_views_per_post', '<=', $request->getAvgViewsPerPostTo());
        })
        ->whereNotNull($request->getSort())
        ->offset($request->getOffset())
        ->limit($request->getLimit())
        ->orderBy($request->getSort(), $request->getSortDirection())
        ->get();
    }

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

    public function cursor(): \Iterator
    {
        return Group::cursor();
    }
}