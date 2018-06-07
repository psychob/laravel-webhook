<?php

Route::group(['prefix' => 'api/v1/webhook', 'middleware' => []], function () {
    Route::post('/test', '\PsychoB\WebHook\Http\Controllers\WebHookController@receive');

    Route::get('/list/inbound', '\PsychoB\WebHook\Http\Controllers\WebHookController@listInbounds');
    Route::get('/list/outbound', '\PsychoB\WebHook\Http\Controllers\WebHookController@listOutbounds');
});
