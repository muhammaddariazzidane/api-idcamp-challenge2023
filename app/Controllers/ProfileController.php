<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class ProfileController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        helper('auth_helper');

        return $this->respond([
            'status' => 200,
            'message' => 'Berhasil mendapatkan data pengguna yang masuk',
            'user' => auth_user(),
        ], 200);
    }
}
