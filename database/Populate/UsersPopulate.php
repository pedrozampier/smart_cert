<?php

namespace Database\Populate;

use App\Models\User;

class UsersPopulate
{
    public static function populate(): void
    {
        $admin = new User();
        $admin->name = 'Administrador';
        $admin->email = 'admin@example.com';
        $admin->password = 'admin123';
        $admin->password_confirmation = 'admin123';
        $admin->is_admin = 1;
        $admin->is_active = 1;
        $admin->phone = '(11) 91234-5678';
        $admin->cpf = '123.456.789-01';
        $admin->save();

        $user = new User();
        $user->name = 'Usuário Comum';
        $user->email = 'user@example.com';
        $user->password = 'user123';
        $user->password_confirmation = 'user123';
        $user->is_admin = 0;
        $user->is_active = 1;
        $user->phone = '(11) 98765-4321';
        $user->cpf = '987.654.321-00';
        $user->save();

        echo "Dois usuários criados com sucesso.\n";
    }
}
