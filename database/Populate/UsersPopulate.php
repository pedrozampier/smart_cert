<?php

namespace Database\Populate;

use App\Models\User;

class UsersPopulate
{
    public static function populate(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'encrypted_password' => 'admin*1212',
            'is_admin' => 1,
            'is_active' => 1,
            'phone' => '1234567890',
            'cpf' => '12345678901'
        ]);

        User::create([
            'name' => 'Usuário comum',
            'email' => 'user@example.com',
            'encrypted_password' => 'user*1212',
            'is_admin' => 0,
            'is_active' => 1,
            'phone' => '0987654321',
            'cpf' => '10987654321'
        ]);

        echo "Dois usuários criados com sucesso.\n";
    }
}
