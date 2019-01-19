<?php

Route::get('/test2', function (\App\Services\ElasticSearchService $service, \Illuminate\Http\Request $request) {
//    throw new Exception('test6');
//    $service->searchGroup(request());
});
Route::get('/api/groups', function () {
    $query = \App\Models\Group::query();

    if (request()->has('title')) { $query = $query->where('title', 'like', '%' . request()->input('title') . '%'); }
    if (request()->has('members_from')) { $query = $query->where('members', '>=', request()->input('members_from')); }
    if (request()->has('members_to')) { $query = $query->where('members', '<=', request()->input('members_to')); }
    if (request()->has('type_id')) { $query = $query->whereIn('type_id', explode(',', request()->input('type_id'))); }
    if (request()->has('country')) { $query = $query->where('country_code', request()->input('country')); }
    if (request()->has('state')) { $query = $query->where('state_code', request()->input('state')); }
    if (request()->has('city')) { $query = $query->where('city_code', request()->input('city')); }
    if (request()->has('is_verified')) { $query = $query->where('is_verified', true); }
    if (request()->has('is_closed')) { $query = $query->where('is_closed', true); }
    if (request()->has('is_adult')) { $query = $query->where('is_adult', true); }
    if (request()->has('posts_from')) { $query = $query->where('posts', '>=', request()->input('posts_from')); }
    if (request()->has('posts_to')) { $query = $query->where('posts_to', '<=', request()->input('posts_to')); }
    if (request()->has('likes_from')) { $query = $query->where('likes', '>=', request()->input('likes_from')); }
    if (request()->has('likes_to')) { $query = $query->where('likes', '<=', request()->input('likes_to')); }
    if (request()->has('avg_posts_from')) { $query = $query->where('avg_posts', '>=', request()->input('avg_posts_from')); }
    if (request()->has('avg_posts_to')) { $query = $query->where('avg_posts', '<=', request()->input('avg_posts_to')); }
    if (request()->has('avg_comments_per_post_from')) { $query = $query->where('avg_comments_per_post', '>=', request()->input('avg_comments_per_post_from')); }
    if (request()->has('avg_comments_per_post_to')) { $query = $query->where('avg_comments_per_post', '<=', request()->input('avg_comments_per_post_to')); }
    if (request()->has('avg_likes_per_post_from')) { $query = $query->where('avg_likes_per_post', '>=', request()->input('avg_likes_per_post_from')); }
    if (request()->has('avg_likes_per_post_to')) { $query = $query->where('avg_likes_per_post', '<=', request()->input('avg_likes_per_post_to')); }
    if (request()->has('avg_shares_per_post_from')) { $query = $query->where('avg_shares_per_post', '>=', request()->input('avg_shares_per_post_from')); }
    if (request()->has('avg_shares_per_post_to')) { $query = $query->where('avg_shares_per_post', '<=', request()->input('avg_shares_per_post_to')); }
    if (request()->has('avg_views_per_post_from')) { $query = $query->where('avg_views_per_post', '>=', request()->input('avg_views_per_post_from')); }
    if (request()->has('avg_views_per_post_to')) { $query = $query->where('avg_views_per_post', '<=', request()->input('avg_views_per_post_to')); }
    if (request()->has('sort')) {
        $query = $query->whereNotNull(request()->input('sort'))->orderBy(request()->input('sort'), request()->input('direction', 'desc') == 'desc' ? 'desc' : 'asc');
    } else {
        $query = $query->orderBy('members', 'desc');
    }

    return response()->json(
        $query
            ->offset(request()->input('offset', 0))
            ->limit(request()->input('limit', 100))
            ->get()
    )->header('Access-Control-Allow-Origin', '*');
});

Route::get('/api/groups/{group}', function (\App\Models\Group $group) {
    return new \App\Http\Resources\GroupResource($group);
});

Route::middleware([\App\Http\Middleware\AccessControl::class])->group(function () {
    Route::get('/api/groups/{group}/links', function (\App\Models\Group $group) {
        return \App\Http\Resources\LinkResource::collection($group->links()->with('post')->get()->sortByDesc('post.date'));
    });

    Route::get('/api/groups/{group}/statistics', function (\App\Models\Group $group) {
        return [];
    });

    Route::post('/api/ads', function (\Illuminate\Http\Request $request) {
        $query = \App\Models\Post::query()
            ->where('is_ad', true)
            ->whereNotNull('export_hash');

        if ($request->has('groupId') && !empty($request->input('groupId'))) { $query->whereIn('group_id', $request->input('groupId')); }
        if (!empty($request->input('dates.from'))) { $query->where('date', '>=', (new \Carbon\Carbon($request->input('dates.from')))->timezone('Europe/Moscow')->startOfDay()); }
        if (!empty($request->input('dates.to'))) { $query->where('date', '<=', (new \Carbon\Carbon($request->input('dates.to')))->timezone('Europe/Moscow')->endOfDay()); }

        foreach (['likes', 'comments', 'shares', 'views'] as $property) {
            if ($request->input($property . '.from') !== null) {
                $query->where($property, '>=', $request->input($property . '.from'));
            }
            if ($request->input($property . '.to') !== null) {
                $query->where($property, '<=', $request->input($property . '.to'));
            }
        }

        if ($request->input('url') !== null) {
            $query->whereExists(function ($subQuery) use ($request) {
                $subQuery
                    ->select(\Illuminate\Support\Facades\DB::raw(1))
                    ->from('links')
                    ->whereRaw('posts.post_id = links.post_id')
                    ->where(function ($whereQuery) use ($request) {
                        $whereQuery->orWhere('url', 'like', '%' . $request->input('url') . '%');
                    });
            });
        }

        if ($request->input('is_video') !== null) {
            $query->where('is_video', (int)$request->input('is_video'));
        }

        if ($request->input('is_gif') !== null) {
            $query->where('is_gif', (int)$request->input('is_gif'));
        }

        switch ($request->input('is_shared')) {
            case false:
                $query->whereNull('shared_group_id');
                break;
            case true:
                $query->whereNotNull('shared_group_id');
                break;
        }

//echo $query
//    ->orderByDesc('likes')
//    ->offset(request()->input('offset', 0))
//    ->limit(request()->input('limit', 10))->toSql();
        return \App\Http\Resources\PostResource::collection(
            $query
                ->orderByDesc('likes')
                ->offset(request()->input('offset', 0))
                ->limit(request()->input('limit', 10))
                ->get()
        );
    });

    Route::get('/api/dictionary/groups', function () {
        $query = \App\Models\Group::query()->select(['id', 'title'])->orderBy('title', 'asc');

        if (request()->has('title')) { $query = $query->where('title', 'ilike', '%' . request()->input('title') . '%'); }

        return new \App\Http\Resources\GroupShortCollection(
            $query
                ->offset(request()->input('offset', 0))
                ->limit(request()->input('limit', 100))
                ->get()
        );
    });
});

Route::get('/api/countries', 'CountryController@countries');
Route::get('/api/countries/{countryCode}/states', 'CountryController@states');
Route::get('/api/countries/{countryCode}/states/{stateCode}/cities', 'CountryController@cities');

Route::get('/test/phpinfo', function () {
    phpinfo();
});

Route::get('/test/env', function () {
    echo '<pre>';
    print_r($_ENV);
    echo '</pre>';
});