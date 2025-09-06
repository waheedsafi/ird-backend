<?php

namespace App\Http\Controllers\v1\auth;

use Sway\Support\JWTTokenGenerator;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function refreshToken()
    {
        $response = JWTTokenGenerator::refreshToken();
        $data = $response->getData(true); // Convert to associative array
        $cookie = cookie(
            'access_token',
            $data['access_token'],
            60 * 24 * 30,
            '/',
            null,                          // null: use current domain
            true,                 // secure only in production
            true,                         // httpOnly
            false,                         // raw
            'None' // for dev, use 'None' to allow cross-origin if needed
        );
        return response()->json(
            [
                "type" => $data['type'],
                "access_token" => $data['access_token'],
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        )->cookie($cookie);
    }
}
