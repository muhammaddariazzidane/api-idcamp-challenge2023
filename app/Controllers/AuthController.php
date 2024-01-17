<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;

class AuthController extends BaseController
{
    use ResponseTrait;

    protected $userModel;

    public function __construct()
    {
        $this->userModel  = new User();
    }

    public function login()
    {
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $this->userModel->where('email', $email)->first();

        if (!$user) return $this->failNotFound('Akun Tidak ditemukan');

        $verify_password = password_verify($password, $user->password);

        if (!$verify_password) return $this->failValidationError('Kata sandi tidak cocok');

        $key = "ehrbdhsysgd77rebj22u98eyr3b";
        $iat = time(); // current timestamp value
        $exp = $iat + 86400; // 86400 seconds = 1 day

        $payload = [
            "iat" => $iat, //Time the JWT issued at
            "exp" => $exp, // Expiration time of token
            "user" => $user,
        ];

        $token = JWT::encode($payload, $key, 'HS256');

        return $this->respond([
            'status' => 200,
            'message' => 'Berhasil Masuk',
            'token' => $token,
            'user' => $user,
        ], 200);
    }

    public function register()
    {

        $rules = [
            'name' => 'required',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors());

        $data = [
            'name'     => $this->request->getVar('name'),
            'email'     => $this->request->getVar('email'),
            'password'  => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT),
        ];

        $this->userModel->save($data);

        return $this->respondCreated([
            'status' => 201,
            'message' => 'Berhasil Daftar',
        ]);
    }
}
