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

Route::middleware([\App\Http\Middleware\AccessControl::class])->group(function () {
    Route::get('/api/groups/{group}', 'GroupController@group');
    Route::get('/api/groups/{group}/links', 'GroupController@links');
    Route::get('/api/groups/{group}/statistics', 'GroupController@statistics');

    Route::post('/api/ads', 'AdController@ads');

    Route::get('/api/dictionary/groups', 'GroupController@groupsShort');
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