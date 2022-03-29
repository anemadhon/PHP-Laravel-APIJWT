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

        Route::get('threads/{category}/categories', [\App\Http\Controllers\API\V1\ThreadController::class, 'byCategory'])->name('threads.by.category');

        Route::apiResource('threads', \App\Http\Controllers\API\V1\ThreadController::class)->scoped([
            'thread' => 'slug'
        ]);
        
        Route::apiResource('threads.comments', \App\Http\Controllers\API\V1\ThreadCommentController::class)->scoped([
            'thread' => 'slug',
            'comment' => 'slug'
        ])->except(['index', 'show']);

        Route::group([
            'prefix' => 'me',
            'as' => 'me.'
        ], function()
        {
            Route::get('profile', [\App\Http\Controllers\API\V1\UserAuthenticatedController::class, 'getProfile'])->name('profile');
            Route::put('profile', [\App\Http\Controllers\API\V1\UserAuthenticatedController::class, 'updateProfile'])->name('profile');

            Route::get('threads', [\App\Http\Controllers\API\V1\UserAuthenticatedController::class, 'threads'])->name('threads');
            Route::get('threads/likes', [\App\Http\Controllers\API\V1\UserAuthenticatedController::class, 'likeThreads'])->name('threads.likes');
            Route::get('threads/unlikes', [\App\Http\Controllers\API\V1\UserAuthenticatedController::class, 'unlikeThreads'])->name('threads.unlikes');
            
            Route::get('comments', [\App\Http\Controllers\API\V1\UserAuthenticatedController::class, 'comments'])->name('comments');
        });
    });
});
