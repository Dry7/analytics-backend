<?php

Route::middleware([\App\Http\Middleware\AccessControl::class])->group(function () {

    Route::post('/api/groups', 'GroupController@groups');
    Route::get('/api/groups/{group}', 'GroupController@group');
    Route::get('/api/groups/{group}/links', 'GroupController@links');
    Route::get('/api/groups/{group}/statistics', 'GroupController@statistics');
    Route::get('/api/dictionary/groups', 'GroupController@groupsShort');

    Route::post('/api/ads', 'AdController@ads');

    Route::get('/api/countries', 'CountryController@countries');
    Route::get('/api/countries/{countryCode}/states', 'CountryController@states');
    Route::get('/api/countries/{countryCode}/states/{stateCode}/cities', 'CountryController@cities');
});

Route::get('/test/phpinfo', function () {
    phpinfo();
});

Route::get('/test/env', function () {
    echo '<pre>';
    print_r($_ENV);
    echo '</pre>';
});