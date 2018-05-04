<?php

Route::get('/', function () {
    app(\App\Services\Html\VKService::class)->test('club3129');
    return view('welcome');
});
