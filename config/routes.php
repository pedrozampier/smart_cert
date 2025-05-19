<?php

use App\Controllers\HomeController;
use Core\Router\Route;
use App\Controllers\AuthController;

Route::middleware('guest')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('root');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [HomeController::class, 'adminDashboard']);
    });

    Route::get('/logout', [AuthController::class, 'logout']);

    Route::get('/user/dashboard', [HomeController::class, 'userDashboard']);
});