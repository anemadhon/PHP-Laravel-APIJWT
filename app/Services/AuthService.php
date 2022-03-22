<?php

namespace App\Services;

use App\Http\Resources\LoginCollection;

class AuthService
{
    public function response(int $code, $message = null, $token = null, $error = null)
    {
        return (new LoginCollection($code, $message, $token, $error));
    }
}
