<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class AccessCest
{
    // 1.1 Tentativa de acesso a área restrita sem autenticação
    public function acessoRestritoSemAutenticacao(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/dashboard');
        $I->see('Você deve estar logado para acessar essa página');
        $I->seeInCurrentUrl('/');
    }

    // 1.2 Tentativa de autenticação com dados incorretos
    public function autenticacaoComDadosInvalidos(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->fillField('email', 'email@invalido.com');
        $I->fillField('password', 'senhaerrada');
        $I->click('Enter');
        $I->see('Credenciais inválidas');
    }

    // 1.3 Autenticação bem-sucedida - usuário normal
    public function autenticacaoUsuarioNormalComDadosValidos(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->fillField('email', 'user@example.com');
        $I->fillField('password', 'user*1212');
        $I->click('Enter');
        $I->seeInCurrentUrl('/user/dashboard');
    }

    // 1.3 Autenticação bem-sucedida - admin
    public function autenticacaoAdminComDadosValidos(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->fillField('email', 'admin@example.com');
        $I->fillField('password', 'admin*1212');
        $I->click('Enter');
        $I->seeInCurrentUrl('/admin/dashboard');
    }

    // 1.4 Logout
    public function testLogout(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->fillField('email', 'user@example.com');
        $I->fillField('password', 'user*1212');
        $I->click('Enter');
        
        $I->amOnPage('/logout');
        $I->seeInCurrentUrl('/');
        $I->amOnPage('/user/dashboard');
        $I->seeInCurrentUrl('/');
    }

    // 2.1 Rotas autenticadas - usuário normal
    public function acessoUsuarioNormalARotaProtegida(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->fillField('email', 'user@example.com');
        $I->fillField('password', 'user*1212');
        $I->click('Enter');
        $I->amOnPage('/user/dashboard');
    }

    // 2.1 Rotas autenticadas - admin
    public function acessoAdminARotaProtegida(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->fillField('email', 'admin@example.com');
        $I->fillField('password', 'admin*1212');
        $I->click('Enter');
        $I->amOnPage('/admin/dashboard');
    }

    // Teste de acesso negado para usuário normal em área admin
    public function acessoNegadoUsuarioNormalEmAreaAdmin(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->fillField('email', 'user@example.com');
        $I->fillField('password', 'user*1212');
        $I->click('Enter');
        $I->amOnPage('/admin/dashboard');
        $I->see('Você deve ser administrador para acessar essa página');
    }

    // 2.2 Rotas públicas
    public function acessoARotaPublicaSemAutenticacao(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->see('Home Page');
    }

    // 2.3 Rotas públicas que não devem permitir usuários autenticados
    public function loginNaoDisponivelParaUsuarioAutenticado(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->fillField('email', 'user@example.com');
        $I->fillField('password', 'user*1212');
        $I->click('Enter');
        
        $I->amOnPage('/');
        $I->seeInCurrentUrl('/user/dashboard');
        $I->dontSee('form');
    }
}