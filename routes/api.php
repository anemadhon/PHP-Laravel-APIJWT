<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api'
], function()
{
    Route::post('login', [\App\Http\Controllers\API\V1\AuthController::class, 'login'])->name('login');

    Route::group(['middleware' => 'auth:api'], function()
    {
        Route::group([
            'prefix' => 'auth',
            'as'=> 'auth.',
        ], function()
        {
            Route::post('logout', [\App\Http\Controllers\API\V1\AuthController::class, 'logout'])->name('logout');
            Route::post('refresh', [\App\Http\Controllers\API\V1\AuthController::class, 'refresh'])->name('refresh'); 
        });

        Route::apiResource('threads', \App\Http\Controllers\API\V1\ThreadController::class)->scoped([
            'thread' => 'slug'
        ]);
    });
});
