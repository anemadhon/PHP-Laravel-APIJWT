<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\LoginCollection;
use App\Http\Resources\ErrorResponseCollection;

class AuthController extends Controller
{
    public function __construct()
    {
        return auth()->shouldUse('api');
    }

    public function login(LoginRequest $request)
    {
        $authentication = auth()->attempt($request->validated());

        if (!$authentication) {

            return new ErrorResponseCollection(401, 'Unauthorized', [
                'message' => 'Please Check your Credentials'
            ]);
        }

        $token = [
            'type' => 'bearer',
            'token' => $authentication,
            'expires' => auth()->factory()->getTTL() * 60
        ];

        return new LoginCollection(200, 'User Logged in Successfully', $token);
    }

    public function refresh()
    {
        $token = [
            'type' => 'bearer',
            'token' => auth()->refresh(true, true),
            'expires' => auth()->factory()->getTTL() * 60
        ];

        return new LoginCollection(200, 'Token Refreshed Successfully', $token);
    }

    public function logout()
    {
        auth()->logout();

        return new LoginCollection(204);
    }
}
