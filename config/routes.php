<?php

use App\Controllers\HomeController;
use App\Controllers\UsersController;
use Core\Router\Route;
use App\Controllers\AuthenticationsController;

Route::get('/', [AuthenticationsController::class, 'new'])->name('root');
Route::get('/login', [AuthenticationsController::class, 'new'])->name('users.login');
Route::post('/login', [AuthenticationsController::class, 'authenticate'])->name('users.authenticate');

Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthenticationsController::class, 'destroy'])->name('users.logout');

    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [HomeController::class, 'adminDashboard'])->name('admin.dashboard');


        Route::get('/users/new', [UsersController::class, 'new'])->name('users.new');
        Route::post('/users', [UsersController::class, 'create'])->name('users.create');
        Route::get('/users', [UsersController::class, 'index'])->name('users.index');
        Route::get('/users/page/{page}', [UsersController::class, 'index'])->name('users.paginate');
        Route::get('/users/{id}', [UsersController::class, 'show'])->name('users.show');
        Route::get('/users/{id}/edit', [UsersController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UsersController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UsersController::class, 'destroy'])->name('users.destroy');
    });

    Route::get('/user/dashboard', [HomeController::class, 'userDashboard'])->name('user.dashboard');
});