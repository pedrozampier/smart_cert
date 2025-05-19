<?php

namespace App\Middleware;

use Core\Http\Middleware\Middleware;
use Core\Http\Request;
use Lib\Authentication\Auth;
use Lib\FlashMessage;

class GuestMiddleware implements Middleware
{
    public function handle(Request $request): void
    {
        if (Auth::check()) {
            $route = Auth::user()->admin ? '/admin/dashboard' : '/user/dashboard';
            $this->redirectTo($route);
        }
    }

    private function redirectTo(string $location): void
    {
        header('Location: ' . $location);
        exit;
    }
}
