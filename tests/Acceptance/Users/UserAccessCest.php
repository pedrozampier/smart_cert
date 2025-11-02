<?php

namespace Tests\Acceptance\Users;

use Tests\Acceptance\BaseAcceptanceCest;
use Tests\Support\AcceptanceTester;

class UserAccessCest extends BaseAcceptanceCest
{
    // 2.1 - Rotas autenticadas não devem ser acessíveis por usuários não autenticados

    public function indexNaoAcessivelSemAutenticacao(AcceptanceTester $page): void
    {
        $page->amOnPage('/users');
        $page->see('Você deve estar logado para acessar essa página');
    }

    public function newNaoAcessivelSemAutenticacao(AcceptanceTester $page): void
    {
        $page->amOnPage('/users/new');
        $page->see('Você deve estar logado para acessar essa página');
    }

    public function showNaoAcessivelSemAutenticacao(AcceptanceTester $page): void
    {
        $page->amOnPage('/users/1');
        $page->see('Você deve estar logado para acessar essa página');
    }

    public function editNaoAcessivelSemAutenticacao(AcceptanceTester $page): void
    {
        $page->amOnPage('/users/1/edit');
        $page->see('Você deve estar logado para acessar essa página');
    }
}
