<?php

namespace App\Controllers;

use App\Models\User;
use Lib\Authentication\Auth;
use Lib\FlashMessage;
use core\helpers;

class AuthController
{
    public function loginForm()
    {
        return redirect('auth/login');
    }

    public function login()
    {
        $email = $_POST['email'] ?? '';
        $senha = $_POST['password'] ?? '';

        $user = User::findByEmail($email);

        if (!$user || !password_verify($senha, $user->encrypted_password)) {
            FlashMessage::danger("Credenciais inválidas");
            return redirect('/');
        }

        Auth::login($user);

        if ($user->is_admin) {
            return redirect('/admin/dashboard');
        } else {
            return redirect('/user/dashboard');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
