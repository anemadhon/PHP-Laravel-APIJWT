<?php

namespace App\Http\Controllers\API\V1;

use App\Models\User;
use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\LoginCollection;
use App\Http\Resources\UserResource;

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

            $token = null;
            $error = [
                'message' => 'Please Check your Credentials'
            ];

            return (new AuthService())->response(401, 'Unauthorized', $token, $error);
        }

        $token = [
            'type' => 'bearer',
            'token' => $authentication,
            'expires' => auth()->factory()->getTTL() * 60
        ];

        return (new AuthService())->response(200, 'User Logged in Successfully', $token);
    }

    public function refresh()
    {
        $token = [
            'type' => 'bearer',
            'token' => auth()->refresh(true, true),
            'expires' => auth()->factory()->getTTL() * 60
        ];

        return (new AuthService())->response(200, 'Token Refreshed Successfully', $token);
    }

    public function logout()
    {
        auth()->logout();
        
        return (new AuthService())->response(204, 'User Logged out Successfully');
    }
}
