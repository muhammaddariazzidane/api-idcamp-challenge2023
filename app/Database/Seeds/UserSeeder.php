<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['name' => 'user', 'email' => 'user@gmail.com', 'password' => password_hash('user123', PASSWORD_DEFAULT), 'is_admin' => false],
            ['name' => 'admin', 'email' => 'admin@gmail.com', 'password' => password_hash('admin', PASSWORD_DEFAULT), 'is_admin' => true]
        ];

        return  $this->db->table('users')->insertBatch($data);
    }
}
