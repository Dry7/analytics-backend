<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Requests\AdRequest;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AdService
{
    /**
     * @param AdRequest $request
     *
     * @return Collection
     */
    public function ads(AdRequest $request): Collection
    {
        return Post::query()
            ->where('is_ad', true)
            ->whereNotNull('export_hash')
            ->when($request->has('groupId') && !empty($request->input('groupId')), function (Builder $query) use ($request) {
                return $query->whereIn('group_id', $request->getGroupId());
            })
            ->when(!empty($request->getDatesFrom()), function (Builder $query) use ($request) {
                return $query->where('date', '>=', $request->getDatesFrom()->startOfDay());
            })
            ->when(!empty($request->getDatesTo()), function (Builder $query) use ($request) {
                return $query->where('date', '<=', $request->getDatesTo()->endOfDay());
            })
            ->when(!empty($request->getUrl()), function (Builder $query) use ($request) {
                return $query->whereExists(function (Builder $subQuery) use ($request) {
                    return $subQuery
                        ->select(\Illuminate\Support\Facades\DB::raw(1))
                        ->from('links')
                        ->whereRaw('posts.post_id = links.post_id')
                        ->where(function ($whereQuery) use ($request) {
                            return $whereQuery->orWhere('url', 'like', '%' . $request->getUrl() . '%');
                        });
                    });
            })
            ->when($request->getIsVideo() !== null, function (Builder $query) use ($request) {
                return $query->where('is_video', $request->getIsVideo());
            })
            ->when($request->getIsGif() !== null, function (Builder $query) use ($request) {
                return $query->where('is_gif', $request->getIsGif());
            })
            ->when($request->getIsShared() === true, function (Builder $query) {
                return $query->whereNotNull('shared_group_id');
            })
            ->when($request->getIsShared() === false, function (Builder $query) {
                return $query->whereNull('shared_group_id');
            })
            ->when($request->getLikesFrom() !== null, function (Builder $query) use ($request) {
                return $query->where('likes', '>=', $request->getLikesFrom());
            })
            ->when($request->getLikesTo() !== null, function (Builder $query) use ($request) {
                return $query->where('likes', '<=', $request->getLikesTo());
            })
            ->when($request->getCommentsFrom() !== null, function (Builder $query) use ($request) {
                return $query->where('comments', '>=', $request->getCommentsFrom());
            })
            ->when($request->getCommentsTo() !== null, function (Builder $query) use ($request) {
                return $query->where('comments', '<=', $request->getCommentsTo());
            })
            ->when($request->getSharesFrom() !== null, function (Builder $query) use ($request) {
                return $query->where('shares', '>=', $request->getSharesFrom());
            })
            ->when($request->getSharesTo() !== null, function (Builder $query) use ($request) {
                return $query->where('shares', '<=', $request->getSharesTo());
            })
            ->when($request->getViewsFrom() !== null, function (Builder $query) use ($request) {
                return $query->where('views', '>=', $request->getViewsFrom());
            })
            ->when($request->getViewsTo() !== null, function (Builder $query) use ($request) {
                return $query->where('views', '<=', $request->getViewsTo());
            })
            ->offset($request->getOffset())
            ->limit($request->getLimit())
            ->orderByDesc('likes')
            ->get();
    }
}
