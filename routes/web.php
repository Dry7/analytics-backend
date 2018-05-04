<?php

Route::get('/', function () {
    app(\App\Services\Html\VKService::class)->test('brain4you');
    return view('welcome');
});
