<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Config\Services;

function auth_user()
{
    $request = Services::request();

    $key = "ehrbdhsysgd77rebj22u98eyr3b";

    $header = $request->getServer('HTTP_AUTHORIZATION');

    $response  = [
        'status' => 401,
        'message'   => 'Unauthorized'
    ];

    if (!$header) return Services::response()->setStatusCode(401)->setJSON($response);

    $token = explode(' ', $header)[1];

    $user = JWT::decode($token, new Key($key, 'HS256'));

    $auth_user = (object)[
        'id' => $user->user->id,
        'name' => $user->user->name,
        'email' => $user->user->email,
        "is_admin" => $user->user->is_admin,
        "created_at" => $user->user->created_at,
        "updated_at" => $user->user->updated_at
    ];
    return $auth_user;
}
