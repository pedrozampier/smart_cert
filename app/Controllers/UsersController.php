<?php

namespace App\Controllers;

use App\Models\User;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\FlashMessage;

class UsersController extends Controller
{
    public function index(Request $request): void
    {
        $paginator = User::paginate(page: $request->getParam('page', 1));
        $users = $paginator->registers();

        $title = 'Usuários Cadastrados';

        if ($request->acceptJson()) {
            $this->renderJson('users/index', compact('paginator', 'users', 'title'));
        } else {
            $this->render('users/index', compact('paginator', 'users', 'title'));
        }
    }

    public function show(Request $request): void
    {
        $params = $request->getParams();

        $user = User::findById($params['id']);

        $title = "Visualização do Usuário #{$user->id}";
        $this->render('users/show', compact('user', 'title'));
    }

    public function new(): void
    {
        $user = new User();

        $title = 'Novo Usuário';
        $this->render('users/new', compact('user', 'title'));
    }

    public function create(Request $request): void
    {
        $params = $request->getParams();
        $user = new User($params['user']);

        if ($user->save()) {
            FlashMessage::success('Usuário cadastrado com sucesso!');
            $this->redirectTo(route('users.index'));
        } else {
            FlashMessage::danger('Existem dados incorretos! Por favor, verifique!');
            $title = 'Novo Usuário';
            $this->render('users/new', compact('user', 'title'));
        }
    }

    public function edit(Request $request): void
    {
        $params = $request->getParams();
        $user = User::findById($params['id']);

        $title = "Editar Usuário #{$user->id}";
        $this->render('users/edit', compact('user', 'title'));
    }

    public function update(Request $request): void
    {
        $id = $request->getParam('id');
        $params = $request->getParam('user');

        $user = User::findById($id);
        $user->name = $params['name'];
        $user->email = $params['email'];
        $user->phone = $params['phone'] ?? null;
        $user->cpf = $params['cpf'] ?? null;
        $user->is_active = isset($params['is_active']) ? (bool)$params['is_active'] : $user->is_active;
        $user->is_admin = isset($params['is_admin']) ? (bool)$params['is_admin'] : $user->is_admin;

        // Só atualiza a senha se foi fornecida
        if (!empty($params['password'])) {
            $user->password = $params['password'];
            $user->password_confirmation = $params['password_confirmation'] ?? '';
        }

        if ($user->save()) {
            FlashMessage::success('Usuário atualizado com sucesso!');
            $this->redirectTo(route('users.index'));
        } else {
            FlashMessage::danger('Existem dados incorretos! Por favor, verifique!');
            $title = "Editar Usuário #{$user->id}";
            $this->render('users/edit', compact('user', 'title'));
        }
    }

    public function destroy(Request $request): void
    {
        $params = $request->getParams();

        $user = User::findById($params['id']);
        $user->destroy();

        FlashMessage::success('Usuário removido com sucesso!');
        $this->redirectTo(route('users.index'));
    }
}
