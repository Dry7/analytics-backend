<?php

Route::get('/api/groups', function () {
    $query = \App\Models\Group::query();

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
            ->take(100)
            ->get()
    )->header('Access-Control-Allow-Origin', '*');
});

Route::get('/api/countries', function () {
    return response()->json(app(\App\Services\CountryService::class)->getCountries())->header('Access-Control-Allow-Origin', '*');
});

Route::get('/api/countries/{countryCode}/states', function ($countryCode) {
    return response()->json(app(\App\Services\CountryService::class)->getStates($countryCode))->header('Access-Control-Allow-Origin', '*');
});

Route::get('/api/countries/{countryCode}/states/{stateCode}/cities', function ($countryCode, $stateCode) {
    return response()->json(app(\App\Services\CountryService::class)->getCities($countryCode, $stateCode))->header('Access-Control-Allow-Origin', '*');
});