<?php

namespace Tests\Acceptance\Users;

use App\Models\User;
use Tests\Acceptance\BaseAcceptanceCest;
use Tests\Support\AcceptanceTester;

class UserCest extends BaseAcceptanceCest
{
    // 1.1 - Tentativa de cadastro com dados incorretos
    public function tentativaDeCadastroComDadosIncorretos(AcceptanceTester $page): void
    {
        $admin = $this->criarAdmin();
        $page->login($admin->email, '123456');

        $page->amOnPage('/users/new');
        $page->fillField('user[name]', 'Teste');

        $page->click('Cadastrar Usuário');

        $page->see('Existem dados incorretos! Por favor, verifique!');
    }

    // 1.2 - Tentativa de cadastro bem-sucedida
    public function tentativaDeCadastroBemSucedida(AcceptanceTester $page): void
    {
        $admin = $this->criarAdmin();
        $page->login($admin->email, '123456');

        $page->amOnPage('/users/new');
        $page->fillField('user[name]', 'João Silva');
        $page->fillField('user[email]', 'joao@example.com');
        $page->fillField('user[cpf]', '98765432100');
        $page->fillField('user[phone]', '11987654321');
        $page->fillField('user[password]', '123456');
        $page->fillField('user[password_confirmation]', '123456');
        $page->click('Cadastrar Usuário');

        $page->wait(1);

        $page->see('Usuário cadastrado com sucesso!');
        $page->seeInCurrentUrl('/users');
        $page->see('João Silva');
        $page->see('joao@example.com');
    }

    // 1.3 - Tentativa de atualização com dados incorretos
    public function tentativaDeAtualizacaoComDadosIncorretos(AcceptanceTester $page): void
    {
        $admin = $this->criarAdmin();
        $user = $this->criarUsuario('Maria Santos', 'maria@example.com', '11122233344');

        $page->login($admin->email, '123456');
        $page->amOnPage("/users/{$user->id}/edit");
        $page->wait(1); 

        $page->fillField('user[email]', 'email-invalido');
        $page->click('Atualizar Usuário');

        $page->wait(1);

        $page->see('Existem dados incorretos! Por favor, verifique!');
        $page->seeInCurrentUrl("/users/{$user->id}");
    }

    // 1.4 - Tentativa de atualização bem-sucedida
    public function tentativaDeAtualizacaoBemSucedida(AcceptanceTester $page): void
    {
        $admin = $this->criarAdmin();
        $user = $this->criarUsuario('Carlos Lima', 'carlos@example.com', '55566677788');

        $page->login($admin->email, '123456');
        $page->amOnPage("/users/{$user->id}/edit");
        $page->wait(1);

        $page->fillField('user[name]', 'Carlos Lima Atualizado');
        $page->fillField('user[phone]', '11999887766');
        $page->click('Atualizar Usuário');

        $page->see('Usuário atualizado com sucesso!');
        $page->seeInCurrentUrl('/users');
        $page->see('Carlos Lima Atualizado');
    }

    // 1.5 - Listagem de todos os registros
    public function listagemDeTodosOsRegistros(AcceptanceTester $page): void
    {
        $admin = $this->criarAdmin();
        $user1 = $this->criarUsuario('Usuário 1', 'usuario1@example.com', '11111111100');
        $user2 = $this->criarUsuario('Usuário 2', 'usuario2@example.com', '22222222200');

        $page->login($admin->email, '123456');
        $page->amOnPage('/users');

        $page->see('Mostrando 1 - 3 de 3 ');
    }


    private function criarAdmin(): User
    {
        $admin = new User([
            'name' => 'Admin User',
            'email' => 'admin@smartcert.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'cpf' => '12345678901'
        ]);
        $admin->is_admin = true;
        $admin->save();

        return $admin;
    }

    private function criarUsuario(string $name, string $email, string $cpf): User
    {
        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => '123456',
            'password_confirmation' => '123456',
            'cpf' => $cpf
        ]);
        $user->save();

        return $user;
    }
}
