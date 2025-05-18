<?php

namespace App\Controllers;

use Core\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        $title = 'Home Page';
        $this->render('login/index', compact('title'));
    }

    public function adminDashboard(): void
    {
        $title = 'Admin Dashboard';
        $this->render('admin/home/index', compact('title'));
    }

    public function userDashboard(): void
    {
        $title = 'User Dashboard';
        $this->render('home/index', compact('title'));
    }
}
