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
            'admin' => 1
        ]);

        User::create([
            'name' => 'Usuário comum',
            'email' => 'user@example.com',
            'encrypted_password' => 'user*1212',
            'admin' => 0
        ]);

        echo "Dois usuários criados com sucesso.\n";
    }
}
