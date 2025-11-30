<?php

use App\Controllers\HomeController;
use App\Controllers\LogosController;
use App\Controllers\UsersController;
use App\Controllers\EventsController;
use App\Controllers\EventParticipantsController;
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

    Route::get('/logos/new', [LogosController::class, 'new'])->name('logos.new');
    Route::post('/logos', [LogosController::class, 'create'])->name('logos.create');
    Route::get('/logos', [LogosController::class, 'index'])->name('logos.index');
    Route::get('/logos/page/{page}', [LogosController::class, 'index'])->name('logos.paginate');
    Route::get('/logos/{id}', [LogosController::class, 'show'])->name('logos.show');
    Route::delete('/logos/{id}', [LogosController::class, 'destroy'])->name('logos.destroy');

    Route::get('/events/new', [EventsController::class, 'new'])->name('events.new');
    Route::get('/events/all', [EventParticipantsController::class, 'index'])
        ->name('event.participants');
    Route::get('/events/all/page/{page}', [EventParticipantsController::class, 'index'])
        ->name('event.participants.paginate');
    Route::get('/events/my-events', [EventParticipantsController::class, 'myEvents'])
        ->name('event.participants.my-events');

    Route::post('/events', [EventsController::class, 'create'])->name('events.create');
    Route::get('/events', [EventsController::class, 'index'])->name('events.index');
    Route::get('/events/page/{page}', [EventsController::class, 'index'])->name('events.paginate');
    Route::get('/events/{id}', [EventsController::class, 'show'])->name('events.show');
    Route::get('/events/{id}/edit', [EventsController::class, 'edit'])->name('events.edit');
    Route::put('/events/{id}', [EventsController::class, 'update'])->name('events.update');
    Route::delete('/events/{id}', [EventsController::class, 'destroy'])->name('events.destroy');

    Route::get('/events/{id}/participants', [EventParticipantsController::class, 'manage'])
        ->name('event.participants.manage');
    Route::post('/events/{event_id}/participants/{participant_id}/add', [EventParticipantsController::class, 'addParticipant'])
        ->name('event.participants.add');
    Route::post('/events/{event_id}/participants/{participant_id}/remove', [EventParticipantsController::class, 'removeParticipant'])
        ->name('event.participants.remove');

    Route::get('/user/dashboard', [HomeController::class, 'userDashboard'])->name('user.dashboard');
});