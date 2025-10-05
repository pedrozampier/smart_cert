<?php

namespace App\Middleware;

use Core\Http\Middleware\Middleware;
use Core\Http\Request;
use Lib\Authentication\Auth;
use Lib\FlashMessage;

class AdminMiddleware implements Middleware
{
    public function handle(Request $request): void
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            FlashMessage::danger('Você deve ser administrador para acessar essa página');
            $this->redirectTo('/user/dashboard');
        }
    }

    private function redirectTo(string $location): void
    {
        header('Location: ' . $location);
        exit;
    }
}
