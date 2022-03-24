<?php

namespace App\Http\Middleware;

use App\Http\Resources\ErrorResponseCollection;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    protected function unauthenticated($request, array $guards)
    {
        abort(new ErrorResponseCollection(401, 'Unauthenticated', [
            'message' => 'Please login to continue'
        ]));
    }
}
